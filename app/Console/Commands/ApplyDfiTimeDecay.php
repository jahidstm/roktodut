<?php

namespace App\Console\Commands;

use App\Models\DonorResponseLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ApplyDfiTimeDecay extends Command
{
    protected $signature = 'donor:dfi-decay {--days=30} {--factor=0.12}';

    protected $description = 'Apply time-decay to donor DFI scores for users not contacted recently.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $factor = (float) $this->option('factor');
        $factor = max(0.01, min(0.5, $factor));

        $cutoff = now()->subDays($days);

        $lastNotifiedSub = DonorResponseLog::query()
            ->selectRaw('donor_id, MAX(notified_at) AS last_notified_at')
            ->groupBy('donor_id');

        $query = User::query()
            ->leftJoinSub($lastNotifiedSub, 'last_notify', function ($join) {
                $join->on('users.id', '=', 'last_notify.donor_id');
            })
            ->where(function (Builder $builder) use ($cutoff) {
                $builder
                    ->whereNull('last_notify.last_notified_at')
                    ->orWhere('last_notify.last_notified_at', '<=', $cutoff);
            })
            ->where('users.dfi_score', '>', 0)
            ->select(['users.id', 'users.dfi_score']);

        $this->info('Applying DFI time decay...');
        $updated = 0;

        $query->chunkById(500, function ($users) use ($factor, &$updated) {
            foreach ($users as $user) {
                $current = (float) $user->dfi_score;
                $next = round(max(0.0, $current * (1 - $factor)), 2);

                if ($next !== $current) {
                    User::where('id', $user->id)->update(['dfi_score' => $next]);
                    $updated++;
                }
            }
        });

        $this->info("DFI time decay applied. Updated: {$updated}");

        return self::SUCCESS;
    }
}
