<?php

namespace App\Notifications;

use App\Models\Ambulance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AmbulanceVerifiedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ambulance $ambulance)
    {
        //
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

    private function buildPayload(): array
    {
        return [
            'title' => '✅ অ্যাম্বুলেন্স ভেরিফাইড!',
            'message' => "আপনার সাবমিট করা অ্যাম্বুলেন্স '{$this->ambulance->name}' ভেরিফাই করা হয়েছে। সাধারণ মানুষের সুবিধার্থে তথ্যটি ডিরেক্টরিতে যুক্ত হয়েছে। ধন্যবাদ!",
            'url' => route('ambulances.index'),
            'urgency' => 'normal',
            'created_at' => now()->toISOString(),
        ];
    }
}
