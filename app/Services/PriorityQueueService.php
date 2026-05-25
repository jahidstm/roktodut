<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PriorityQueueService
{
    public const SUPER_CRITICAL_SCORE = 100;
    public const GOLD_SCORE = 50;
    public const SILVER_SCORE = 25;

    public function applyPriorityForRequest(User $requester, BloodRequest $request, bool $useSuperCriticalToken): void
    {
        DB::transaction(function () use ($requester, $request, $useSuperCriticalToken): void {
            $freshRequester = User::query()->whereKey($requester->id)->lockForUpdate()->first();

            if (!$freshRequester) {
                return;
            }

            $tier = (string) ($freshRequester->priority_tier ?? 'standard');
            $isTiered = in_array($tier, ['gold', 'silver'], true);

            if ($useSuperCriticalToken && $isTiered && $freshRequester->super_critical_tokens > 0) {
                $freshRequester->decrement('super_critical_tokens');
                $request->update([
                    'is_super_critical' => true,
                    'priority_score' => self::SUPER_CRITICAL_SCORE,
                ]);
                return;
            }

            $baseline = match ($tier) {
                'gold' => self::GOLD_SCORE,
                'silver' => self::SILVER_SCORE,
                default => 0,
            };

            $request->update([
                'is_super_critical' => false,
                'priority_score' => $baseline,
            ]);
        }, 3);
    }
}
