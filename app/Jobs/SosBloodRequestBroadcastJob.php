<?php

namespace App\Jobs;

use App\Enums\BloodComponentType;
use App\Models\BloodRequest;
use App\Models\DonorResponseLog;
use App\Models\User;
use App\Services\DfiCalculationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * SosBloodRequestBroadcastJob
 *
 * Dedicated high-priority job for Emergency SOS requests.
 * Key differences from SmartBloodRequestBroadcastJob:
 *   - DFI threshold check     → BYPASSED (life > score)
 *   - Quiet Hours check       → BYPASSED (2am notifications allowed)
 *   - Availability Calendar   → BYPASSED (is_super_critical = true)
 *   - topK                    → 20 (strict limit, quality > quantity)
 *   - Radius                  → 10km (hyper-local first)
 *   - Ranking                 → Response Rate weighted (best responders first)
 *   - Queue                   → 'sos' (high-priority queue)
 */
class SosBloodRequestBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MAX_DISTANCE_KM      = 10.0;   // Hyper-local: 10km for SOS
    private const DEFAULT_TOP_K        = 20;      // Strict cap — SMS bankruptcy prevention
    private const CANDIDATE_MULTIPLIER = 10;      // Candidate pool = topK × 10
    private const MAX_CANDIDATES       = 200;

    public function __construct(
        public int $bloodRequestId,
        public int $topK = self::DEFAULT_TOP_K
    ) {}

    public function handle(DfiCalculationService $dfiService): void
    {
        $bloodRequest = BloodRequest::with(['district', 'upazila', 'hospital'])->find($this->bloodRequestId);
        if (!$bloodRequest) {
            return;
        }

        $now        = now();
        $candidates = $this->fetchSosCandidates($bloodRequest);

        if ($candidates->isEmpty()) {
            return;
        }

        $candidateIds  = $candidates->pluck('id')->map(fn($id) => (int) $id)->all();
        $responseStats = $this->getResponseStats($candidateIds);

        $ranked = [];

        foreach ($candidates as $donor) {
            $donorId     = (int) $donor->id;
            $distanceKm  = round((float) ($donor->distance_km ?? 9999.0), 2);

            $stats       = $responseStats[$donorId] ?? ['accepted' => 0, 'total' => 0];
            $responseRate = $stats['total'] > 0
                ? round($stats['accepted'] / $stats['total'], 4)
                : 0.0;

            // ── SOS Ranking: Response Rate is king, distance is secondary ──
            // DFI is intentionally excluded for SOS (life > score)
            $rankScore = $this->sosRankScore($responseRate, $distanceKm, (bool) $donor->is_ready_now);

            $ranked[] = [
                'donor_id'                => $donorId,
                'distance_km'             => $distanceKm,
                'days_since_last_donation'=> 0,
                'temporal_hour'           => (int) $now->hour,
                'is_weekend'              => (bool) $now->isWeekend(),
                'historical_response_rate'=> $responseRate,
                'rank_score'              => $rankScore,
            ];
        }

        if ($ranked === []) {
            return;
        }

        // Sort by rank descending (best responders first)
        usort($ranked, fn(array $a, array $b) => $b['rank_score'] <=> $a['rank_score']);
        $topK = array_slice($ranked, 0, max(1, $this->topK));

        // Reuse the existing DispatchEmergencyAlertsJob for actual notification delivery
        // (FCM + Telegram + DB notification + response log persistence)
        DispatchEmergencyAlertsJob::dispatch(
            bloodRequestId: $bloodRequest->id,
            rankedDonors: $topK
        )->afterCommit();
    }

    // ── Fetch SOS candidates: ALL filters bypassed, high response rate sorted ─
    private function fetchSosCandidates(BloodRequest $request)
    {
        $bloodGroup = is_object($request->blood_group)
            ? $request->blood_group->value
            : (string) $request->blood_group;

        $candidateLimit = min(self::MAX_CANDIDATES, $this->topK * self::CANDIDATE_MULTIPLIER);

        $query = User::query()
            ->where('is_donor', true)
            ->where('blood_group', $bloodGroup)
            ->where('is_shadowbanned', false)
            ->when(
                $request->requested_by !== null,
                fn(Builder $q) => $q->where('id', '!=', $request->requested_by)
            );

        // ── Component-based cooldown check (medical safety — cannot bypass) ──
        $component = $request->component_type instanceof BloodComponentType
            ? $request->component_type->value
            : (string) ($request->component_type ?? BloodComponentType::WHOLE_BLOOD->value);

        if ($component === BloodComponentType::PLASMA->value) {
            $query->where(function (Builder $sq) {
                $sq->whereNull('last_plasma_donated_at')
                   ->orWhere('last_plasma_donated_at', '<=', now()->subDays(28));
            });
        } elseif ($component === BloodComponentType::PLATELETS->value) {
            $query->where(function (Builder $sq) {
                $sq->whereNull('last_platelet_donated_at')
                   ->orWhere('last_platelet_donated_at', '<=', now()->subDays(14));
            });
        } else {
            $query->where(function (Builder $sq) {
                $sq->whereNull('last_whole_blood_donated_at')
                   ->orWhere('last_whole_blood_donated_at', '<=', now()->subDays(120));
            })->where(function (Builder $sq) {
                $sq->whereNull('last_donated_at')
                   ->orWhere('last_donated_at', '<=', now()->subDays(120));
            });
        }

        // ── NOTE: DFI threshold, is_available, Quiet Hours, Calendar ─────────
        // ALL bypassed for SOS (is_super_critical = true guarantees this)
        // We intentionally do NOT add `is_available = true` filter here.

        // ── Spatial filter: GPS if available, otherwise district fallback ──
        if ($request->latitude !== null && $request->longitude !== null) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;

            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

            $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("users.*, {$haversine} AS distance_km", [$lat, $lng, $lat])
                ->having('distance_km', '<=', self::MAX_DISTANCE_KM)
                ->orderBy('distance_km');
        } else {
            // GPS unavailable → district fallback
            $query
                ->where('district_id', $request->district_id)
                ->selectRaw('users.*, 9999.00 AS distance_km')
                ->orderBy('id');
        }

        return $query->limit($candidateLimit)->get();
    }

    /**
     * SOS-specific ranking: Response Rate is primary signal.
     * DFI is excluded because life is more important than donor fatigue score.
     */
    private function sosRankScore(float $responseRate, float $distanceKm, bool $isReadyNow): float
    {
        // Response Rate (0–1) × 200 makes it dominant over distance penalty
        $score = ($responseRate * 200) - ($distanceKm * 2);

        if ($isReadyNow) {
            $score += 10;
        }

        return round($score, 2);
    }

    /**
     * @param array<int> $donorIds
     * @return array<int, array{accepted:int, total:int}>
     */
    private function getResponseStats(array $donorIds): array
    {
        if ($donorIds === []) {
            return [];
        }

        $stats = DonorResponseLog::query()
            ->whereIn('donor_id', $donorIds)
            ->selectRaw('donor_id, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS accepted_count, COUNT(*) AS total_count', ['accepted'])
            ->groupBy('donor_id')
            ->get();

        $response = [];
        foreach ($stats as $row) {
            $response[(int) $row->donor_id] = [
                'accepted' => (int) $row->accepted_count,
                'total'    => (int) $row->total_count,
            ];
        }

        return $response;
    }
}
