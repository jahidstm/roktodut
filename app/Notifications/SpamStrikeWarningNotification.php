<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpamStrikeWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public int $strikeCount, public bool $isShadowbanned)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->isShadowbanned) {
            return [
                'title' => '⛔ আপনার অ্যাকাউন্ট শ্যাডোব্যান করা হয়েছে',
                'message' => 'একাধিক স্প্যাম রিপোর্টের কারণে আপনার অ্যাকাউন্টটি সাময়িকভাবে রেস্ট্রিক্ট করা হয়েছে। আপনার রক্তের অনুরোধগুলো আর পাবলিক ফিডে দেখাবে না। বিস্তারিত জানতে অ্যাডমিনের সাথে যোগাযোগ করুন।',
                'url' => '#',
                'icon' => 'ban',
                'color' => 'text-rose-600'
            ];
        }

        return [
            'title' => '⚠️ স্প্যাম ওয়ার্নিং স্ট্রাইক',
            'message' => "আপনার একটি রক্তের অনুরোধ স্প্যাম হিসেবে রিপোর্ট করা হয়েছে এবং অ্যাডমিন কর্তৃক স্ট্রাইক দেওয়া হয়েছে। বর্তমান স্ট্রাইক: {$this->strikeCount}। ২ টি স্ট্রাইক পেলে অ্যাকাউন্ট রেস্ট্রিক্ট করা হবে।",
            'url' => '#',
            'icon' => 'alert-triangle',
            'color' => 'text-amber-500'
        ];
    }
}
