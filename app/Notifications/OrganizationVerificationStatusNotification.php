<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationVerificationStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'approved') {
            return [
                'type' => 'success',
                'title' => 'অর্গানাইজেশন ভেরিফাইড! 🎉',
                'message' => 'অভিনন্দন! আপনার অর্গানাইজেশনের ভেরিফিকেশন সফল হয়েছে। এখন আপনি প্ল্যাটফর্মের সকল ফিচার ব্যবহার করতে পারবেন।',
                'url' => route('org.dashboard'),
                'icon' => 'check-circle'
            ];
        }

        return [
            'type' => 'error',
            'title' => 'ভেরিফিকেশন বাতিল ❌',
            'message' => 'দুঃখিত, আপনার অর্গানাইজেশনের ভেরিফিকেশন বাতিল করা হয়েছে। কারণ: ' . ($this->reason ?? 'অজানা'),
            'url' => route('org.dashboard'),
            'icon' => 'x-circle'
        ];
    }
}
