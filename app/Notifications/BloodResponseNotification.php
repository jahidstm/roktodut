<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
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

    public function toArray(object $notifiable): array
    {
        return $this->buildPayload();
    }

    private function buildPayload(): array
    {
        $message = match($this->status) {
            'accepted' => "রোগীর পক্ষ থেকে আপনাকে ডোনার হিসেবে নির্বাচন করা হয়েছে।",
            'verified' => "আপনার রক্তদান সফলভাবে ভেরিফাইড হয়েছে! আপনাকে পয়েন্ট এবং ব্যাজ দেওয়া হয়েছে।",
            'disputed' => "আপনার রক্তদান ক্লেইমটি ডিসপুট করা হয়েছে। অ্যাডমিন রিভিউ করবে।",
            'declined', 'withdrawn' => "{$this->responder->name} রিকোয়েস্ট থেকে সরে দাঁড়িয়েছেন।",
            'rejected' => "রোগী দুঃখজনকভাবে আপনার সাড়া গ্রহণ করতে পারেননি।",
            'pending' => "{$this->responder->name} আপনার রক্তের রিকোয়েস্টে রক্ত দিতে আগ্রহ প্রকাশ করেছেন।",
            default => "আপনার রক্তের রিকোয়েস্টে একটি আপডেট এসেছে।"
        };

        $bloodGroup = $this->bloodRequest->blood_group instanceof \App\Enums\BloodGroup
            ? $this->bloodRequest->blood_group->value
            : (string) $this->bloodRequest->blood_group;
        $urgency = $this->bloodRequest->urgency instanceof \App\Enums\UrgencyLevel
            ? $this->bloodRequest->urgency->value
            : (string) $this->bloodRequest->urgency;

        return [
            'request_id' => $this->bloodRequest->id,
            'patient_name' => $this->bloodRequest->patient_name,
            'responder_id' => $this->responder->id,
            'responder_name' => $this->responder->name,
            'status' => $this->status,
            'message' => $message,
            'blood_group' => $bloodGroup,
            'urgency' => $urgency,
            'url' => route('requests.show', $this->bloodRequest->id),
            'created_at' => now()->toISOString(),
        ];
    }
}
