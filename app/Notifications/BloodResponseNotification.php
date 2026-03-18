<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return ['database']; // আপাতত আমরা ডাটাবেসে সেভ করব
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $action = $this->status === 'accepted' ? 'এক্সেপ্ট' : 'ডিক্লাইন';
        
        return [
            'request_id' => $this->bloodRequest->id,
            'patient_name' => $this->bloodRequest->patient_name,
            'responder_id' => $this->responder->id,
            'responder_name' => $this->responder->name,
            'status' => $this->status,
            'message' => "{$this->responder->name} আপনার রক্তের রিকোয়েস্টটি {$action} করেছেন।"
        ];
    }
}