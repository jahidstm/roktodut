<?php

namespace App\Notifications;

use App\Models\BloodRequestResponse;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $response;
    public $sender;
    public $messagePreview;

    public function __construct(BloodRequestResponse $response, User $sender, string $messagePreview)
    {
        $this->response = $response;
        $this->sender = $sender;
        $this->messagePreview = mb_substr($messagePreview, 0, 50) . (mb_strlen($messagePreview) > 50 ? '...' : '');
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
        // Load the relationship safely so we don't hit an N+1 problem
        $this->response->loadMissing('bloodRequest');
        
        $bloodGroup = $this->response->bloodRequest?->blood_group instanceof \App\Enums\BloodGroup
            ? $this->response->bloodRequest->blood_group->value
            : (string) ($this->response->bloodRequest?->blood_group ?? '');

        $urgency = $this->response->bloodRequest?->urgency instanceof \App\Enums\UrgencyLevel
            ? $this->response->bloodRequest->urgency->value
            : (string) ($this->response->bloodRequest?->urgency ?? 'normal');

        return [
            'response_id' => $this->response->id,
            'request_id' => $this->response->blood_request_id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => "নতুন চ্যাট মেসেজ: {$this->messagePreview}",
            'blood_group' => $bloodGroup,
            'urgency' => $urgency,
            'url' => route('chat.show', $this->response->id),
            'created_at' => now()->toISOString(),
        ];
    }
}
