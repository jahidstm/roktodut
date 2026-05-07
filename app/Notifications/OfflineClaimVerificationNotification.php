<?php

namespace App\Notifications;

use App\Models\OfflineDonationClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OfflineClaimVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly OfflineDonationClaim $claim,
        private readonly string $verificationUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => '🩸 অফলাইন রক্তদান যাচাই প্রয়োজন',
            'message' => sprintf(
                '%s জানিয়েছেন তিনি %s তারিখে আপনার জন্য রক্ত দিয়েছেন। অনুগ্রহ করে যাচাই করুন।',
                $this->claim->donor?->name ?? 'একজন ডোনার',
                $this->claim->donation_date?->format('d M, Y') ?? '-'
            ),
            'url' => $this->verificationUrl,
            'claim_id' => $this->claim->id,
            'created_at' => now()->toISOString(),
        ];
    }
}
