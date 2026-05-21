<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogNotificationTelemetry
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(\Illuminate\Notifications\Events\NotificationSent $event): void
    {
        // We only want to track BloodRequestMatchedNotification sent to Users
        if ($event->notification instanceof \App\Notifications\BloodRequestMatchedNotification && 
            $event->notifiable instanceof \App\Models\User) {
            
            // Extract request_id from the database payload (we defined this earlier in toDatabase)
            $payload = $event->notification->toDatabase($event->notifiable);

            \App\Models\DonorTelemetryLog::create([
                'user_id' => $event->notifiable->id,
                'blood_request_id' => $payload['request_id'] ?? null,
                'notification_type' => 'fcm_push', // Assuming standard push
                'ignored' => true, // Default true, updated when they respond
            ]);
        }
    }
}
