<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodRequestMatchedNotification;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendEmergencyBloodRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param array<int> $donorIds */
    public function __construct(
        public int $bloodRequestId,
        public string $districtName,
        public array $donorIds
    ) {}

    public function handle(TelegramService $telegram): void
    {
        if (empty($this->donorIds)) {
            return;
        }

        $bloodRequest = BloodRequest::with(['district', 'upazila'])->find($this->bloodRequestId);

        if (!$bloodRequest) {
            return;
        }

        // ─── প্রস্তুত করুন Alert Data ──────────────────────────────
        $bloodGroup = is_object($bloodRequest->blood_group)
            ? $bloodRequest->blood_group->value
            : (string) $bloodRequest->blood_group;

        $location = implode(', ', array_filter([
            $bloodRequest->upazila?->name,
            $bloodRequest->district?->name ?? $this->districtName,
        ]));

        $neededAt = $bloodRequest->needed_at
            ? $bloodRequest->needed_at->format('d M, Y — h:i A')
            : 'যত দ্রুত সম্ভব (ASAP)';

        $alertData = [
            'blood_group'  => $bloodGroup,
            'component'    => $bloodRequest->componentLabel(),
            'hospital'     => $bloodRequest->hospital ?? 'উল্লেখ নেই',
            'location'     => $location ?: $this->districtName,
            'bags_needed'  => $bloodRequest->bags_needed ?? 1,
            'needed_at'    => $neededAt,
            'urgency'      => is_object($bloodRequest->urgency)
                ? $bloodRequest->urgency->value
                : (string) $bloodRequest->urgency,
            'request_url'  => route('requests.show', $bloodRequest->id),
        ];

        // ─── Chunking: 25 donors at a time to prevent API throttling ──
        User::query()
            ->whereIn('id', $this->donorIds)
            ->chunk(25, function ($donors) use ($bloodRequest, $telegram, $alertData) {

                // ১. In-App Notification
                Notification::send($donors, new BloodRequestMatchedNotification($bloodRequest, $this->districtName));

                // ২. Telegram Alert
                $telegramDonors = $donors->filter(fn($d) => !empty($d->telegram_chat_id));

                foreach ($telegramDonors as $donor) {
                    $telegram->sendBloodAlert($donor->telegram_chat_id, $alertData);
                }

                // Artificial delay to prevent Telegram API rate limit
                sleep(1);
            });
    }
}
