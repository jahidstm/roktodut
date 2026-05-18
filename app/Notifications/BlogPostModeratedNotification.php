<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogPostModeratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Post $post,
        public string $status, // 'approved' or 'rejected'
        public ?string $reason = null
    ) {}

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
        if ($this->status === 'approved') {
            return [
                'title' => '🎉 আপনার ব্লগ পোস্ট প্রকাশিত হয়েছে!',
                'message' => "আপনার লেখা \"{$this->post->title}\" পোস্টটি অ্যাডমিন কর্তৃক অনুমোদিত ও প্রকাশিত হয়েছে।",
                'url' => route('blog.show', $this->post->slug),
                'icon' => 'check-circle',
                'color' => 'text-emerald-500'
            ];
        }

        return [
            'title' => '❌ ব্লগ পোস্ট বাতিল করা হয়েছে',
            'message' => "আপনার লেখা \"{$this->post->title}\" পোস্টটি প্রকাশ করা সম্ভব হয়নি。" . ($this->reason ? " কারণ: {$this->reason}" : ''),
            'url' => '#',
            'icon' => 'x-circle',
            'color' => 'text-rose-500'
        ];
    }
}
