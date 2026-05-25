<?php

namespace App\Services;

use App\Models\DonorResponseLog;
use App\Models\User;
use Illuminate\Support\Carbon;

class DfiCalculationService
{
    public const IGNORED_WEIGHT = 12.0;
    public const DAYS_PENALTY_PER_DAY = 0.15;
    public const MAX_SCORE = 100.0;
    public const MAX_DAYS_PENALTY = 30.0;
    public const DEFAULT_IGNORE_AFTER_HOURS = 24;

    public function calculateScore(int $ignoredRequests, int $daysSinceLastDonation): float
    {
        $ignoredPenalty = $ignoredRequests * self::IGNORED_WEIGHT;
        $daysPenalty = min(self::MAX_DAYS_PENALTY, $daysSinceLastDonation * self::DAYS_PENALTY_PER_DAY);

        return round(min(self::MAX_SCORE, max(0.0, $ignoredPenalty + $daysPenalty)), 2);
    }

    public function calculateForDonor(User $donor, int $ignoredRequests, ?Carbon $now = null): float
    {
        $daysSinceLastDonation = $this->resolveDaysSinceLastDonation($donor, $now);
        return $this->calculateScore($ignoredRequests, $daysSinceLastDonation);
    }

    public function resolveDaysSinceLastDonation(User $donor, ?Carbon $now = null): int
    {
        $now = $now ?? now();
        $lastDonatedAt = $donor->last_whole_blood_donated_at ?? $donor->last_donated_at;

        if (!$lastDonatedAt) {
            return 0;
        }

        $last = $lastDonatedAt instanceof Carbon
            ? $lastDonatedAt
            : Carbon::parse((string) $lastDonatedAt);

        return max(0, $last->diffInDays($now));
    }

    public function countIgnoredRequests(int $donorId, int $afterHours = self::DEFAULT_IGNORE_AFTER_HOURS): int
    {
        $cutoff = now()->subHours($afterHours);

        return DonorResponseLog::query()
            ->where('donor_id', $donorId)
            ->whereIn('status', ['pending', 'ignored'])
            ->where('notified_at', '<=', $cutoff)
            ->count();
    }
}
