<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonorVerificationStatusNotification extends Notification
{
    use Queueable;

    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // আমরা ডেটাবেস নোটিফিকেশন ব্যবহার করছি
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // স্ট্যাটাস অনুযায়ী ডাইনামিক মেসেজ
        $message = $this->status === 'approved' 
            ? 'অভিনন্দন! আপনার এনআইডি ভেরিফিকেশন সফল হয়েছে। আপনি এখন একজন ব্লু-ব্যাজ ভেরিফাইড ডোনার।' 
            : 'দুঃখিত, আপনার এনআইডি ভেরিফিকেশন রিকোয়েস্ট বাতিল করা হয়েছে। সঠিক ছবি দিয়ে আবার চেষ্টা করুন।';

        return [
            'type' => 'verification_status',
            'status' => $this->status,
            'message' => $message,
            'url' => '/dashboard', // ক্লিক করলে ইউজারের ড্যাশবোর্ডে যাবে
        ];
    }
}