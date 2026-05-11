<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredNidData extends Command
{
    protected $signature = 'nid:purge-expired {--dry-run : Preview purge candidates without deleting}';
    protected $description = 'Purge expired private NID data based on retention policy';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $candidates = User::query()
            ->whereNotNull('nid_retention_until')
            ->where('nid_retention_until', '<=', now())
            ->where(function ($q) {
                $q->whereNotNull('nid_path')
                    ->orWhereNotNull('nid_number')
                    ->orWhereNotNull('nid_number_hash');
            })
            ->get(['id', 'nid_path', 'nid_retention_until']);

        if ($candidates->isEmpty()) {
            $this->info('No expired NID records found.');
            return self::SUCCESS;
        }

        $purged = 0;

        foreach ($candidates as $user) {
            if ($isDryRun) {
                $this->line("Would purge NID data for user #{$user->id} (expired at {$user->nid_retention_until}).");
                continue;
            }

            if (!empty($user->nid_path) && Storage::disk('private')->exists($user->nid_path)) {
                Storage::disk('private')->delete($user->nid_path);
            }

            $user->forceFill([
                'nid_path' => null,
                'nid_number' => null,
                'nid_number_hash' => null,
                'nid_status' => 'none',
                'nid_uploaded_at' => null,
                'nid_retention_until' => null,
                'nid_last_accessed_at' => null,
            ])->save();

            AuditLogger::log('privacy.nid.auto_purge', $user, [
                'reason' => 'retention_expired',
            ]);

            $purged++;
        }

        if ($isDryRun) {
            $this->info("Dry run complete. {$candidates->count()} user(s) matched.");
            return self::SUCCESS;
        }

        $this->info("Purged NID data for {$purged} user(s).");
        return self::SUCCESS;
    }
}
