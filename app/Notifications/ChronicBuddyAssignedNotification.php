<?php

namespace App\Notifications;

use App\Models\ChronicRequestSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ChronicBuddyAssignedNotification extends Notification
{
    use Queueable;

    public $subscription;

    public function __construct(ChronicRequestSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

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
        $conditionName = $this->subscription->condition_label ?? 'দীর্ঘমেয়াদী';
        $patientName = $this->subscription->patient_name ?? 'একজন রোগীর';
        
        return [
            'subscription_id' => $this->subscription->id,
            'patient_name' => $this->subscription->patient_name,
            'condition_type' => $this->subscription->condition_type,
            'status' => 'buddy_assigned',
            'message' => "আপনি {$patientName}-এর ({$conditionName}) জন্য নিয়মিত 'ব্লাড বাডি' হিসেবে নির্বাচিত হয়েছেন।",
            'url' => route('donor_profile.dashboard'), // ডোনার ড্যাশবোর্ডের "My Chronic Patients" সেকশনে যাবে
            'created_at' => now()->toISOString(),
        ];
    }
}
