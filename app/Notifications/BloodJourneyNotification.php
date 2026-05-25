<?php

namespace App\Notifications;

use App\Enums\BloodJourneyStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BloodJourneyNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly BloodJourneyStatus $status) {}

    public function via(object $notifiable): array
    {
        if ($this->status === BloodJourneyStatus::DISCARDED) {
            $this->applySuspension($notifiable);
        }

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

    private function buildPayload(): array
    {
        $message = match ($this->status) {
            BloodJourneyStatus::DELIVERED => 'আপনার দেওয়া রক্ত আজ একটি জীবন বাঁচিয়েছে। আপনার অবদানের জন্য কৃতজ্ঞতা।',
            BloodJourneyStatus::DISCARDED => 'আপনার সাম্প্রতিক ডোনেশনের ল্যাব স্ক্রিনিংয়ে একটি অস্বাভাবিকতা পাওয়া গেছে। অনুগ্রহ করে মেডিকেল ক্লিয়ারেন্স প্রদান করুন।',
            default => 'আপনার রক্তদানের অগ্রগতি আপডেট হয়েছে।',
        };

        return [
            'type' => 'blood_journey',
            'status' => $this->status->value,
            'message' => $message,
            'url' => route('dashboard'),
            'created_at' => now()->toISOString(),
        ];
    }

    private function applySuspension(object $notifiable): void
    {
        if (method_exists($notifiable, 'update')) {
            $notifiable->update([
                'is_donor' => false,
                'suspension_reason' => 'Lab screening anomaly',
            ]);
        }
    }
}
