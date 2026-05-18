<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Report $report)
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
        if ($this->report->status === 'resolved') {
            return [
                'title' => '✅ আপনার রিপোর্টটি সমাধান করা হয়েছে',
                'message' => 'আপনার সাবমিট করা রিপোর্টটির ব্যাপারে অ্যাডমিন ব্যবস্থা নিয়েছেন। রক্তদূতকে নিরাপদ রাখতে আপনার অবদানের জন্য ধন্যবাদ!',
                'url' => '#',
                'icon' => 'check-circle',
                'color' => 'text-emerald-500'
            ];
        }

        return [
            'title' => 'ℹ️ রিপোর্টের আপডেট',
            'message' => 'আপনার সাবমিট করা রিপোর্টটি পর্যালোচনা করা হয়েছে এবং বর্তমানে বাতিল (Dismissed) করা হয়েছে।',
            'url' => '#',
            'icon' => 'info',
            'color' => 'text-slate-500'
        ];
    }
}
