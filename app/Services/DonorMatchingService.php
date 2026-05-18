<?php

namespace App\Services;

use App\Enums\BloodComponentType;
use App\Models\BloodRequest;
use App\Models\DonorResponseLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DonorMatchingService
{
    private const FASTAPI_RANKING_URL = 'http://127.0.0.1:8001/api/v1/rank-donors';
    private const MAX_CANDIDATES = 50;
    private const DEFAULT_BATCH_SIZE = 5;
    private const MAX_DISTANCE_KM = 20.0;

    /**
     * Backward-compatible method used in existing flows.
     */
    public function match(BloodRequest $request): EloquentCollection
    {
        $ids = $this->rankDonors($request, self::MAX_CANDIDATES)
            ->pluck('donor_id')
            ->values()
            ->all();

        if ($ids === []) {
            return new EloquentCollection();
        }

        $donorsById = User::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return new EloquentCollection(
            collect($ids)
                ->map(fn (int $id) => $donorsById->get($id))
                ->filter()
                ->values()
                ->all()
        );
    }

    /**
     * Generate AI candidate list:
     * - active/available donors
     * - matching blood group
     * - within 20km when request coordinates are available
     * - not on active cooldown
     */
    public function generateCandidateList(BloodRequest $request, int $limit = self::MAX_CANDIDATES): Collection
    {
        $limit = max(1, min($limit, self::MAX_CANDIDATES));
        $now = now();
        $temporalHour = (int) $now->hour;
        $isWeekend = $now->isWeekend();

        $query = $this->buildBaseQuery($request)
            ->select([
                'id',
                'blood_group',
                'district_id',
                'latitude',
                'longitude',
                'last_donated_at',
                'cooldown_until',
                'is_available',
                'is_donor',
            ]);

        if ($request->latitude !== null && $request->longitude !== null) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;

            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

            $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("{$haversine} AS distance_km", [$lat, $lng, $lat])
                ->having('distance_km', '<=', self::MAX_DISTANCE_KM)
                ->orderBy('distance_km');
        } else {
            $query
                ->where('district_id', $request->district_id)
                ->selectRaw('9999.00 AS distance_km')
                ->orderBy('id');
        }

        $donors = $query->limit($limit)->get();

        if ($donors->isEmpty()) {
            return collect();
        }

        $historicalRates = $this->getHistoricalResponseRates($donors->pluck('id')->all());

        return $donors->map(function (User $donor) use ($historicalRates, $now, $temporalHour, $isWeekend): array {
            $lastDonatedAt = $donor->last_donated_at instanceof Carbon
                ? $donor->last_donated_at
                : ($donor->last_donated_at ? Carbon::parse((string) $donor->last_donated_at) : null);

            $daysSinceLastDonation = $lastDonatedAt
                ? max(0, $lastDonatedAt->diffInDays($now))
                : 365;

            $distanceKm = round((float) ($donor->distance_km ?? 9999.0), 2);

            return [
                'donor_id' => (int) $donor->id,
                'distance_km' => $distanceKm,
                'days_since_last_donation' => $daysSinceLastDonation,
                'temporal_hour' => $temporalHour,
                'is_weekend' => $isWeekend,
                'historical_response_rate' => round((float) ($historicalRates[$donor->id] ?? 0.0), 4),
            ];
        })->values();
    }

    /**
     * Returns ranked candidates with rank/probability for dispatch workflow.
     * Fallback path (service down/timeout): closest donors first.
     */
    public function rankDonors(BloodRequest $request, int $limit = self::DEFAULT_BATCH_SIZE): Collection
    {
        $limit = max(1, $limit);
        $candidates = $this->generateCandidateList($request, self::MAX_CANDIDATES);

        if ($candidates->isEmpty()) {
            return collect();
        }

        try {
            $response = Http::timeout(2)
                ->withHeaders([
                    'X-API-Key' => env('ROKTODUT_AI_SECRET', 'ROKTODUT_AI_SECRET'),
                ])
                ->post(self::FASTAPI_RANKING_URL, [
                    'request_details' => [
                        'request_id' => $request->id,
                        'blood_group' => (string) ($request->blood_group?->value ?? $request->blood_group),
                        'urgency' => (string) $request->urgency,
                        'units_needed' => (int) ($request->units_needed ?? $request->bags_needed ?? 1),
                    ],
                    'candidate_donors' => $candidates->values()->all(),
                ]);

            $response->throw();
            $rankedFromAi = collect($response->json());

            if ($rankedFromAi->isEmpty()) {
                throw new \RuntimeException('Empty ranking response from AI service.');
            }

            $candidateById = $candidates->keyBy('donor_id');

            return $rankedFromAi
                ->filter(fn ($row) => isset($row['donor_id']) && $candidateById->has((int) $row['donor_id']))
                ->map(function (array $row, int $index) use ($candidateById): array {
                    $donorId = (int) $row['donor_id'];
                    $base = $candidateById->get($donorId, []);

                    return array_merge($base, [
                        'probability_score' => isset($row['probability_score']) ? (float) $row['probability_score'] : 0.0,
                        'rank' => isset($row['rank']) ? (int) $row['rank'] : ($index + 1),
                    ]);
                })
                ->sortBy('rank')
                ->values()
                ->take($limit)
                ->values();
        } catch (\Throwable $e) {
            Log::warning('AI donor ranking failed; using distance fallback.', [
                'blood_request_id' => $request->id,
                'message' => $e->getMessage(),
            ]);

            return $candidates
                ->sortBy('distance_km')
                ->values()
                ->take($limit)
                ->values()
                ->map(fn (array $row, int $index) => array_merge($row, [
                    'probability_score' => 0.0,
                    'rank' => $index + 1,
                ]));
        }
    }

    private function buildBaseQuery(BloodRequest $request): Builder
    {
        $bloodGroup = is_object($request->blood_group)
            ? $request->blood_group->value
            : (string) $request->blood_group;

        return User::query()
            ->where('is_donor', true)
            ->where('blood_group', $bloodGroup)
            ->when(
                $request->requested_by !== null,
                fn (Builder $q) => $q->where('id', '!=', $request->requested_by)
            )
            ->where('is_shadowbanned', false)
            ->where(function (Builder $q) {
                $q->whereNull('is_available')->orWhere('is_available', true);
            })
            ->where(function (Builder $q) {
                $q->whereNull('cooldown_until')->orWhere('cooldown_until', '<=', now());
            })
            ->where(function (Builder $q) use ($request) {
                $component = $request->component_type instanceof BloodComponentType
                    ? $request->component_type->value
                    : (string) ($request->component_type ?? BloodComponentType::WHOLE_BLOOD->value);

                if ($component === BloodComponentType::PLASMA->value) {
                    $q->where(function (Builder $sq) {
                        $sq->whereNull('last_plasma_donated_at')
                            ->orWhere('last_plasma_donated_at', '<=', now()->subDays(28));
                    });
                } elseif ($component === BloodComponentType::PLATELETS->value) {
                    $q->where(function (Builder $sq) {
                        $sq->whereNull('last_platelet_donated_at')
                            ->orWhere('last_platelet_donated_at', '<=', now()->subDays(14));
                    });
                } else {
                    $q->where(function (Builder $sq) {
                        $sq->whereNull('last_whole_blood_donated_at')
                            ->orWhere('last_whole_blood_donated_at', '<=', now()->subDays(120));
                    })->where(function (Builder $sq) {
                        $sq->whereNull('last_donated_at')
                            ->orWhere('last_donated_at', '<=', now()->subDays(120));
                    });
                }
            });
    }

    /**
     * @param array<int> $donorIds
     * @return array<int, float>
     */
    private function getHistoricalResponseRates(array $donorIds): array
    {
        if ($donorIds === []) {
            return [];
        }

        $stats = DonorResponseLog::query()
            ->whereIn('donor_id', $donorIds)
            ->selectRaw('donor_id, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS accepted_count, COUNT(*) AS total_count', ['accepted'])
            ->groupBy('donor_id')
            ->get();

        $rates = [];
        foreach ($stats as $row) {
            $total = (int) $row->total_count;
            $accepted = (int) $row->accepted_count;
            $rates[(int) $row->donor_id] = $total > 0 ? round($accepted / $total, 4) : 0.0;
        }

        return $rates;
    }
}

