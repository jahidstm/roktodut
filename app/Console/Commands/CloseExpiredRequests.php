<?php

namespace App\Console\Commands;

use App\Models\BloodRequest;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloseExpiredRequests extends Command
{
    protected $signature = 'requests:expire';

    protected $description = 'Expire old pending/in_progress blood requests (needed_at older than 6 hours).';

    public function handle(): int
    {
        $threshold = now()->subHours(6);

        $idsToExpire = BloodRequest::query()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('needed_at')
            ->where('needed_at', '<', $threshold)
            ->pluck('id')
            ->all();

        if (empty($idsToExpire)) {
            $this->info('No requests to expire.');
            return self::SUCCESS;
        }

        $requestsToExpire = BloodRequest::with('requester')->whereIn('id', $idsToExpire)->get();

        $expiredCount = 0;

        DB::transaction(function () use (&$expiredCount, $idsToExpire, $threshold, $requestsToExpire): void {
            $expiredCount = BloodRequest::query()
                ->whereIn('id', $idsToExpire)
                ->update(['status' => 'expired']);

            foreach ($requestsToExpire as $request) {
                if ($request->requester) {
                    $request->requester->notify(new \App\Notifications\BloodRequestExpiredNotification($request));
                }
            }

            AuditLogger::log(
                action: 'system.expire_request',
                target: ['type' => BloodRequest::class],
                metadata: [
                    'expired_count' => $expiredCount,
                    'request_ids' => $idsToExpire,
                    'threshold' => $threshold->toDateTimeString(),
                ],
                actorUserId: null,
            );
        });

        $this->info("Expired {$expiredCount} request(s).");
        return self::SUCCESS;
    }
}
