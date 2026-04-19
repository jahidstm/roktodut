<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DonorVerificationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $status) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        $broadcastDriver = (string) config('broadcasting.default', 'null');
        if (!in_array($broadcastDriver, ['null', 'log'], true)) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->buildPayload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->buildPayload());
    }

    public function toArray(object $notifiable): array
    {
        return $this->buildPayload();
    }

    private function buildPayload(): array
    {
        $normalizedStatus = in_array($this->status, ['approved', 'verified'], true)
            ? 'verified'
            : 'rejected';

        $message = $normalizedStatus === 'verified'
            ? 'অভিনন্দন! আপনার এনআইডি ভেরিফিকেশন সফল হয়েছে। আপনি এখন একজন ব্লু-ব্যাজ ভেরিফাইড ডোনার।'
            : 'দুঃখিত, আপনার এনআইডি ভেরিফিকেশন রিকোয়েস্ট বাতিল করা হয়েছে। সঠিক ছবি দিয়ে আবার চেষ্টা করুন।';

        return [
            'type' => 'verification_status',
            'status' => $normalizedStatus,
            'message' => $message,
            'url' => route('dashboard'),
            'urgency' => null,
            'blood_group' => null,
            'created_at' => now()->toISOString(),
        ];
    }
}
