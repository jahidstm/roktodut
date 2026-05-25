<?php

namespace Tests\Feature\Notifications;

use App\Enums\BloodJourneyStatus;
use App\Models\User;
use App\Notifications\BloodJourneyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BloodJourneyNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_suspends_user_on_discarded_status()
    {
        $user = User::factory()->create([
            'is_donor' => true,
            'suspension_reason' => null,
        ]);

        $notification = new BloodJourneyNotification(BloodJourneyStatus::DISCARDED);
        $user->notify($notification);

        $user->refresh();

        $this->assertEquals(0, $user->is_donor);
        $this->assertEquals('Lab screening anomaly', $user->suspension_reason);
    }

    public function test_it_does_not_suspend_user_on_delivered_status()
    {
        $user = User::factory()->create([
            'is_donor' => true,
            'suspension_reason' => null,
        ]);

        $notification = new BloodJourneyNotification(BloodJourneyStatus::DELIVERED);
        $user->notify($notification);

        $user->refresh();

        $this->assertEquals(1, $user->is_donor);
        $this->assertNull($user->suspension_reason);
    }
}
