<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodRequestMatchedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendEmergencyBloodRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array<int> $donorIds
     */
    public function __construct(
        public int $bloodRequestId,
        public string $districtName,
        public array $donorIds
    ) {}

    public function handle(): void
    {
        if (empty($this->donorIds)) {
            return;
        }

        $bloodRequest = BloodRequest::find($this->bloodRequestId);

        if (!$bloodRequest) {
            return;
        }

        $donors = User::query()
            ->whereIn('id', $this->donorIds)
            ->get();

        if ($donors->isEmpty()) {
            return;
        }

        Notification::send($donors, new BloodRequestMatchedNotification($bloodRequest, $this->districtName));
    }
}

