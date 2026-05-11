<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_is_not_available_when_feature_is_disabled(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(404);
    }

    public function test_email_verification_send_endpoint_is_not_available_when_feature_is_disabled(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');
        $response->assertStatus(404);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_route_is_not_available(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email/1/invalid-hash');
        $response->assertStatus(404);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
