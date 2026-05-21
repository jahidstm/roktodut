<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateDonorFatigueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donor:calculate-fatigue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates Donor Fatigue Index (DFI) using Exponential Decay Model for predictive dispatch routing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting DFI calculation using Exponential Decay Model...');

        // Fetch all donors (In a real system, you might chunk this)
        \App\Models\User::chunk(500, function ($donors) {
            foreach ($donors as $donor) {
                $this->calculateForDonor($donor);
            }
        });

        $this->info('DFI calculation completed successfully.');
    }

    private function calculateForDonor(\App\Models\User $donor)
    {
        // 1. Get recent telemetry logs for this donor (e.g., last 30 days)
        $logs = \App\Models\DonorTelemetryLog::where('user_id', $donor->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            // No recent activity, base fatigue is 0
            $this->storeFatigueScore($donor->id, 0);
            return;
        }

        // --- Mathematical Model Parameters ---
        $lambda = 0.1; // Decay rate (how fast they recover per day)
        $penaltyPerIgnore = 20; // Score added if they ignore a request
        $penaltyPerLatencySec = 0.05; // Small penalty for responding very late

        $currentFatigue = 0;
        $lastEventDate = null;

        foreach ($logs as $log) {
            $penalty = 0;

            if ($log->ignored) {
                $penalty = $penaltyPerIgnore;
            } else {
                // If they responded, calculate latency penalty
                $latencySec = $log->latency_ms / 1000;
                if ($latencySec > 600) { // If it took more than 10 mins
                    $penalty = min(10, ($latencySec - 600) * $penaltyPerLatencySec);
                }
            }

            if ($lastEventDate) {
                // Apply exponential decay over the time passed between events
                $daysPassed = $lastEventDate->diffInDays($log->created_at);
                // F(t) = F_base * e^(-lambda * t)
                $currentFatigue = $currentFatigue * exp(-$lambda * $daysPassed);
            }

            // Add the new penalty
            $currentFatigue += $penalty;
            $lastEventDate = $log->created_at;
        }

        // Apply decay from the last event up to right now
        if ($lastEventDate) {
            $daysPassedSinceLastEvent = $lastEventDate->diffInDays(now());
            $currentFatigue = $currentFatigue * exp(-$lambda * $daysPassedSinceLastEvent);
        }

        // Cap fatigue score at 100
        $finalScore = min(100, max(0, $currentFatigue));

        $this->storeFatigueScore($donor->id, $finalScore);
    }

    private function storeFatigueScore($userId, $score)
    {
        // Option A: Store in Redis for ultra-fast Geo-Dispatch filtering
        \Illuminate\Support\Facades\Redis::zadd('donor_fatigue', $score, $userId);
        
        // Option B: Also store in Users table (if a column exists)
        // \App\Models\User::where('id', $userId)->update(['fatigue_score' => $score]);
    }
}
