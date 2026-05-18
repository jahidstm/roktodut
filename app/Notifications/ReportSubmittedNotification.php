<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportSubmittedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly \App\Models\Report $report)
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
        return ['database', \App\Notifications\Channels\AdminTelegramChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $category = ucfirst(str_replace('_', ' ', $this->report->category));
        
        return [
            'message' => "নতুন রিপোর্ট জমা পড়েছে (কারণ: {$category})",
            'url' => route('admin.reports.index')
        ];
    }

    public function toAdminTelegram(object $notifiable): string
    {
        $category = ucfirst(str_replace('_', ' ', $this->report->category));
        
        return "⚠️ <b>নতুন রিপোর্ট জমা পড়েছে!</b>\n\n"
             . "<b>কারণ:</b> {$category}\n"
             . "<b>বিস্তারিত:</b> " . \Illuminate\Support\Str::limit($this->report->description, 100) . "\n\n"
             . "🔗 <a href=\"" . route('admin.reports.index') . "\">রিপোর্ট প্যানেলে দেখুন</a>";
    }
}
