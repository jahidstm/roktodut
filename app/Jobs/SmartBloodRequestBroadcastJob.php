<?php

namespace App\Jobs;

use App\Enums\BloodComponentType;
use App\Models\BloodRequest;
use App\Models\DonorAvailability;
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

class SmartBloodRequestBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MAX_DISTANCE_KM = 20.0;
    private const QUIET_HOURS_START = 23;
    private const QUIET_HOURS_END = 6;
    private const MAX_DFI_THRESHOLD = 70.0;
    private const QUIET_HOURS_MIN_RESPONSE_RATE = 0.2;
    private const DEFAULT_TOP_K = 15;
    private const DEFAULT_CANDIDATE_LIMIT = 200;

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

        $now = now();
        $isSuperCritical = (bool) $bloodRequest->is_super_critical;
        $isQuietHours = $this->isQuietHours($now) && !$isSuperCritical;

        $candidates = $this->fetchCandidates($bloodRequest, $this->candidateLimit());
        if ($candidates->isEmpty()) {
            return;
        }

        $candidateIds = $candidates->pluck('id')->map(fn($id) => (int) $id)->all();
        $responseStats = $this->getResponseStats($candidateIds);
        $ignoredCounts = $this->getIgnoredCounts($candidateIds, DfiCalculationService::DEFAULT_IGNORE_AFTER_HOURS);

        $ranked = [];

        foreach ($candidates as $donor) {
            $donorId = (int) $donor->id;
            $ignored = (int) ($ignoredCounts[$donorId] ?? 0);
            $dfiScore = $dfiService->calculateForDonor($donor, $ignored, $now);
            $daysSinceLastDonation = $dfiService->resolveDaysSinceLastDonation($donor, $now);

            $stats = $responseStats[$donorId] ?? ['accepted' => 0, 'total' => 0];
            $responseRate = $stats['total'] > 0 ? round($stats['accepted'] / $stats['total'], 4) : 0.0;

            if (!$isSuperCritical) {
                if ($dfiScore > self::MAX_DFI_THRESHOLD) {
                    continue;
                }

                if ($isQuietHours && $responseRate < self::QUIET_HOURS_MIN_RESPONSE_RATE) {
                    continue;
                }
            }

            $distanceKm = round((float) ($donor->distance_km ?? 9999.0), 2);
            $rankScore = $this->rankScore($responseRate, $distanceKm, $dfiScore, $isSuperCritical, (bool) $donor->is_ready_now);

            $ranked[] = [
                'donor_id' => $donorId,
                'distance_km' => $distanceKm,
                'days_since_last_donation' => $daysSinceLastDonation,
                'temporal_hour' => (int) $now->hour,
                'is_weekend' => (bool) $now->isWeekend(),
                'historical_response_rate' => $responseRate,
                'rank_score' => $rankScore,
            ];

            if (abs((float) $donor->dfi_score - $dfiScore) > 0.01) {
                User::where('id', $donorId)->update(['dfi_score' => $dfiScore]);
            }
        }

        if ($ranked === []) {
            return;
        }

        usort($ranked, fn(array $a, array $b) => $b['rank_score'] <=> $a['rank_score']);
        $topK = array_slice($ranked, 0, max(1, $this->topK));

        DispatchEmergencyAlertsJob::dispatch(
            bloodRequestId: $bloodRequest->id,
            rankedDonors: $topK
        )->afterCommit();
    }

    private function fetchCandidates(BloodRequest $request, int $limit)
    {
        $bloodGroup = is_object($request->blood_group)
            ? $request->blood_group->value
            : (string) $request->blood_group;

        $query = User::query()
            ->where('is_donor', true)
            ->where('blood_group', $bloodGroup)
            ->where('is_shadowbanned', false)
            ->when(
                $request->requested_by !== null,
                fn(Builder $q) => $q->where('id', '!=', $request->requested_by)
            )
            ->where(function (Builder $q) {
                $q->whereNull('is_available')->orWhere('is_available', true);
            })
            ->where(function (Builder $q) {
                $q->whereNull('cooldown_until')->orWhere('cooldown_until', '<=', now());
            });

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
            $query
                ->where('district_id', $request->district_id)
                ->selectRaw('users.*, 9999.00 AS distance_km')
                ->orderBy('id');
        }

        // 🗓️ Calendar Bitmask Filter
        // Super-critical emergencies bypass the calendar — human life > schedule.
        if (!$this->isSuperCritical($request)) {
            $now         = now()->setTimezone('Asia/Dhaka');
            $todayBit    = DonorAvailability::bitForDay($now->dayOfWeek);
            $todayDate   = $now->toDateString();
            $currentTime = $now->format('H:i:s');

            $query->where(function (Builder $q) use ($todayBit, $todayDate, $currentTime) {
                $q->whereDoesntHave(
                    'availabilities',
                    fn (Builder $r) => $r->where('is_active', true)
                )
                ->orWhereHas('availabilities', function (Builder $r) use ($todayBit, $todayDate, $currentTime) {
                    $r->where('is_active', true)
                      ->where(function (Builder $rq) use ($todayBit, $todayDate, $currentTime) {
                          $rq->where(function (Builder $sq) use ($todayBit, $currentTime) {
                              $sq->where('type', 'weekly')
                                 ->whereRaw('(weekdays_bitmask & ?) > 0', [$todayBit])
                                 ->where(fn (Builder $tq) => $tq
                                     ->whereNull('time_from')
                                     ->orWhereRaw('? BETWEEN time_from AND time_to', [$currentTime]));
                          })
                          ->orWhere(function (Builder $sq) use ($todayDate, $currentTime) {
                              $sq->where('type', 'specific_date')
                                 ->where('specific_date', $todayDate)
                                 ->where(fn (Builder $tq) => $tq
                                     ->whereNull('time_from')
                                     ->orWhereRaw('? BETWEEN time_from AND time_to', [$currentTime]));
                          })
                          ->orWhere(function (Builder $sq) use ($todayDate, $currentTime) {
                              $sq->where('type', 'date_range')
                                 ->where('date_from', '<=', $todayDate)
                                 ->where('date_to', '>=', $todayDate)
                                 ->where(fn (Builder $tq) => $tq
                                     ->whereNull('time_from')
                                     ->orWhereRaw('? BETWEEN time_from AND time_to', [$currentTime]));
                          });
                      });
                });
            });
        }

        return $query->limit($limit)->get();
    }

    private function isSuperCritical(BloodRequest $request): bool
    {
        return (bool) $request->is_super_critical;
    }

    /**
     * @param array<int> $donorIds
     * @return array<int, array{accepted:int,total:int}>
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
                'total' => (int) $row->total_count,
            ];
        }

        return $response;
    }

    /**
     * @param array<int> $donorIds
     * @return array<int, int>
     */
    private function getIgnoredCounts(array $donorIds, int $afterHours): array
    {
        if ($donorIds === []) {
            return [];
        }

        $cutoff = now()->subHours($afterHours);

        return DonorResponseLog::query()
            ->whereIn('donor_id', $donorIds)
            ->whereIn('status', ['pending', 'ignored'])
            ->where('notified_at', '<=', $cutoff)
            ->selectRaw('donor_id, COUNT(*) AS ignored_count')
            ->groupBy('donor_id')
            ->pluck('ignored_count', 'donor_id')
            ->map(fn($value) => (int) $value)
            ->all();
    }

    private function isQuietHours(Carbon $now): bool
    {
        $hour = (int) $now->hour;
        return $hour >= self::QUIET_HOURS_START || $hour <= self::QUIET_HOURS_END;
    }

    private function rankScore(float $responseRate, float $distanceKm, float $dfiScore, bool $isSuperCritical, bool $isReadyNow): float
    {
        $score = ($responseRate * 100) - ($distanceKm * 2);

        if (!$isSuperCritical) {
            $score -= $dfiScore;
        }

        if ($isReadyNow) {
            $score += 5;
        }

        return round($score, 2);
    }

    private function candidateLimit(): int
    {
        $limit = max(self::DEFAULT_CANDIDATE_LIMIT, $this->topK * 10);
        return min($limit, 500);
    }
}
