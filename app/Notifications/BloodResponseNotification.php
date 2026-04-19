<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BloodResponseNotification extends Notification
{
    use Queueable;

    public $bloodRequest;
    public $responder;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(BloodRequest $bloodRequest, User $responder, $status)
    {
        $this->bloodRequest = $bloodRequest;
        $this->responder = $responder;
        $this->status = $status;
    }

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
        $action = $this->status === 'accepted' ? 'এক্সেপ্ট' : 'ডিক্লাইন';
        $bloodGroup = $this->bloodRequest->blood_group instanceof \App\Enums\BloodGroup
            ? $this->bloodRequest->blood_group->value
            : (string) $this->bloodRequest->blood_group;
        $urgency = $this->bloodRequest->urgency instanceof \App\Enums\UrgencyLevel
            ? $this->bloodRequest->urgency->value
            : (string) $this->bloodRequest->urgency;

        return [
            'request_id' => $this->bloodRequest->id,
            'patient_name' => $this->bloodRequest->patient_name,
            'responder_id' => $this->responder->id,
            'responder_name' => $this->responder->name,
            'status' => $this->status,
            'message' => "{$this->responder->name} আপনার রক্তের রিকোয়েস্টটি {$action} করেছেন।",
            'blood_group' => $bloodGroup,
            'urgency' => $urgency,
            'url' => route('requests.show', $this->bloodRequest->id),
            'created_at' => now()->toISOString(),
        ];
    }
}
