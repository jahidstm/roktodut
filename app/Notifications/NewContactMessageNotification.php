<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * NewContactMessageNotification
 *
 * নতুন Contact বার্তা এলে সকল Admin ইউজারকে notify করে।
 * Channels: database (persistent) + broadcast (real-time via Reverb)
 *
 * Recipients: User::where('role', 'admin')->get()
 *   → ContactController@store এ notify() কল হবে
 */
class NewContactMessageNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(
        private readonly ContactMessage $contactMessage,
    ) {}

    /**
     * Notification channels:
     * - database: ডাটাবেসে persist করে (reload-এও দেখাবে)
     * - broadcast: Reverb-এর মাধ্যমে real-time push
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * ডাটাবেসে সেভ করার জন্য payload
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->buildPayload();
    }

    /**
     * Reverb broadcast payload (lightweight)
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->buildPayload());
    }

    /**
     * Private channel: শুধু Admin ইউজারই শুনতে পারবে।
     * প্রতিটি Admin-এর নিজস্ব প্রাইভেট চ্যানেলে পৌঁছাবে।
     */
    public function broadcastOn(): PrivateChannel
    {
        // $this->notifiable এখানে inject হয় Laravel-এর notification system থেকে
        return new PrivateChannel('user.' . ($this->notifiable?->id ?? 0));
    }

    /**
     * Frontend Echo-এ listen করার event name।
     * Echo.private('user.X').listen('NewContactMessage', ...)
     */
    public function broadcastAs(): string
    {
        return 'NewContactMessage';
    }

    // ─── Private ────────────────────────────────────────────────────────────

    private function buildPayload(): array
    {
        $preview = mb_substr($this->contactMessage->message, 0, 100)
                 . (mb_strlen($this->contactMessage->message) > 100 ? '...' : '');

        return [
            'contact_message_id' => $this->contactMessage->id,
            'subject'            => $this->contactMessage->subject,
            'sender_name'        => $this->contactMessage->sender_name,
            'sender_email'       => $this->contactMessage->email,
            'preview'            => $preview,
            'message'            => "নতুন যোগাযোগ বার্তা: \"{$this->contactMessage->subject}\" — {$this->contactMessage->sender_name}",
            'url'                => route('admin.support.messages.show', $this->contactMessage->id),
            'urgency'            => null,   // notification bell compat
            'blood_group'        => null,   // notification bell compat
            'created_at'         => now()->toISOString(),
        ];
    }
}
