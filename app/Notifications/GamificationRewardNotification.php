<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GamificationRewardNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly ?string $url = null,
        private readonly int $points = 0
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
            'type' => 'gamification_reward',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url ?? route('dashboard'),
            'points' => $this->points,
            'urgency' => 'normal',
            'blood_group' => null,
            'created_at' => now()->toISOString(),
        ];
    }
}
