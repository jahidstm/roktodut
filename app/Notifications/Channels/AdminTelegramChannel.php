<?php

namespace App\Notifications\Channels;

use App\Services\TelegramService;
use Illuminate\Notifications\Notification;

class AdminTelegramChannel
{
    public function __construct(private TelegramService $telegram)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toAdminTelegram')) {
            $message = $notification->toAdminTelegram($notifiable);
            
            if (!empty($message)) {
                $this->telegram->sendAdminAlert($message);
            }
        }
    }
}
