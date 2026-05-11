<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BloodRequestExpiredNotification extends Notification
{
    use Queueable;

    public $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

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
        return [
            'request_id' => $this->bloodRequest->id,
            'patient_name' => $this->bloodRequest->patient_name,
            'status' => 'expired',
            'message' => "আপনার রক্তের রিকোয়েস্টটি এক্সপায়ার হয়ে গেছে। এখনো রক্ত প্রয়োজন হলে রিনিউ করুন।",
            'url' => route('requests.show', $this->bloodRequest->id),
            'created_at' => now()->toISOString(),
        ];
    }
}
