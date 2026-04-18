<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminTaskNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $message,
        private readonly string $url,
        private readonly string $title = '🛡️ নতুন অ্যাডমিন টাস্ক',
        private readonly ?string $taskType = null,
    ) {}

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

    private function buildPayload(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'task_type' => $this->taskType,
            'urgency' => 'normal',
            'blood_group' => null,
            'created_at' => now()->toISOString(),
        ];
    }
}
