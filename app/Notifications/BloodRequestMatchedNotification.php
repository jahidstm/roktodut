<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * BloodRequestMatchedNotification
 *
 * রক্তের অনুরোধ তৈরি হলে ম্যাচড ডোনারদের notify করে।
 * Channels: database (persistent) + broadcast (real-time via Reverb)
 */
class BloodRequestMatchedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly BloodRequest $bloodRequest,
        private readonly string $districtName,
    ) {}

    /**
     * Notification channels:
     * - database: ডাটাবেসে persist করে (reload-এও দেখাবে)
     * - broadcast: Reverb-এর মাধ্যমে real-time push
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

    /**
     * ডাটাবেসে সেভ করার জন্য payload
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->buildPayload();
    }

    /**
     * Reverb broadcast payload (lightweight — শুধু দরকারি fields)
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->buildPayload());
    }

    // ─── Private ────────────────────────────────────────────────────────────

    private function buildPayload(): array
    {
        $bloodGroup = $this->bloodRequest->blood_group instanceof \App\Enums\BloodGroup
            ? $this->bloodRequest->blood_group->value
            : (string) $this->bloodRequest->blood_group;

        $urgency = $this->bloodRequest->urgency instanceof \App\Enums\UrgencyLevel
            ? $this->bloodRequest->urgency->label()
            : (string) $this->bloodRequest->urgency;

        $message = "{$this->districtName} জেলায় {$bloodGroup} রক্তের জরুরি প্রয়োজন! "
                 . "({$urgency}) — আপনি কি সাহায্য করতে পারবেন?";

        return [
            'request_id'    => $this->bloodRequest->id,
            'blood_group'   => $bloodGroup,
            'urgency'       => $this->bloodRequest->urgency instanceof \App\Enums\UrgencyLevel
                                ? $this->bloodRequest->urgency->value
                                : (string) $this->bloodRequest->urgency,
            'district_name' => $this->districtName,
            'upazila_name'  => $this->bloodRequest->upazila?->name ?? '',
            'message'       => $message,
            'url'           => route('requests.show', $this->bloodRequest->id),
            'created_at'    => now()->toISOString(),
        ];
    }
}
