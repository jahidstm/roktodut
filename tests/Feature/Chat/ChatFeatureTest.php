<?php

namespace Tests\Feature\Chat;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\District;
use App\Models\Division;
use App\Models\Donation;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function usePusherBroadcaster(): void
    {
        config()->set('broadcasting.default', 'pusher');
        config()->set('broadcasting.connections.pusher.key', 'test');
        config()->set('broadcasting.connections.pusher.secret', 'test');
        config()->set('broadcasting.connections.pusher.app_id', 'test');
        config()->set('broadcasting.connections.pusher.options', []);

        // Channel callbacks are registered on the current broadcast driver.
        // In tests, the app boots with broadcasting disabled (null), so we need
        // to re-register channel definitions after switching to a real driver.
        \Illuminate\Support\Facades\Broadcast::connection();
        require base_path('routes/channels.php');
    }

    public function test_chat_message_can_be_sent_and_saved(): void
    {
        [$requester, $donor, $response] = $this->makeResponseWithDonation('matched');

        $payload = ['message' => 'হ্যালো, আমি আসছি।'];

        $this->actingAs($requester)
            ->postJson(route('chat.store', $response), $payload)
            ->assertOk()
            ->assertJsonStructure([
                'message' => ['id', 'sender_id', 'message', 'created_at'],
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'blood_request_response_id' => $response->id,
            'sender_id' => $requester->id,
            'message' => $payload['message'],
        ]);
    }

    public function test_chat_private_channel_allows_only_requester_and_donor(): void
    {
        $this->usePusherBroadcaster();

        [$requester, $donor, $response] = $this->makeResponseWithDonation('matched');
        $other = User::factory()->create();

        $payload = [
            'channel_name' => 'private-chat.response.' . $response->id,
            'socket_id' => '123.456',
        ];

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($requester)
            ->post('/broadcasting/auth', $payload)
            ->assertOk();

        $this->actingAs($donor)
            ->post('/broadcasting/auth', $payload)
            ->assertOk();

        $this->actingAs($other)
            ->post('/broadcasting/auth', $payload)
            ->assertStatus(403);
    }

    public function test_chat_index_resolves_opposite_party_phone_correctly(): void
    {
        [$requester, $donor, $response] = $this->makeResponseWithDonation('matched');

        $this->actingAs($requester)
            ->get(route('chat.show', $response))
            ->assertOk()
            ->assertViewHas('oppositePartyPhone', $donor->phone);

        $this->actingAs($donor)
            ->get(route('chat.show', $response))
            ->assertOk()
            ->assertViewHas('oppositePartyPhone', $requester->phone);
    }

    public function test_chat_index_shows_closed_notice_when_delivered_or_discarded(): void
    {
        [$requester, $donor, $response] = $this->makeResponseWithDonation('delivered');

        $this->actingAs($requester)
            ->get(route('chat.show', $response))
            ->assertOk()
            ->assertSee('এই রক্তদান প্রক্রিয়াটি সম্পন্ন/বাতিল হওয়ায় চ্যাটটি বন্ধ করা হয়েছে');
    }

    public function test_chat_is_blocked_after_delivered_or_discarded(): void
    {
        [$requester, $donor, $response] = $this->makeResponseWithDonation('delivered');

        $this->actingAs($requester)
            ->postJson(route('chat.store', $response), ['message' => 'test'])
            ->assertStatus(403);
    }

    private function makeResponseWithDonation(string $journeyStatus): array
    {
        $division = Division::create([
            'name' => 'Dhaka',
        ]);

        $district = District::create([
            'division_id' => $division->id,
            'name' => 'Dhaka',
        ]);

        $upazila = Upazila::create([
            'district_id' => $district->id,
            'name' => 'Dhanmondi',
        ]);

        $requester = User::factory()->create([
            'phone' => '01700000001',
        ]);

        $donor = User::factory()->create([
            'phone' => '01700000002',
            'is_donor' => true,
        ]);

        $bloodRequest = BloodRequest::create([
            'requested_by' => $requester->id,
            'patient_name' => 'Test Patient',
            'blood_group' => 'A+',
            'bags_needed' => 1,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'upazila_id' => $upazila->id,
            'contact_number' => '01700000003',
            'urgency' => 'normal',
        ]);

        $response = BloodRequestResponse::create([
            'blood_request_id' => $bloodRequest->id,
            'user_id' => $donor->id,
            'status' => 'accepted',
        ]);

        Donation::factory()->create([
            'donor_id' => $donor->id,
            'blood_request_id' => $bloodRequest->id,
            'district' => 'Dhaka',
            'journey_status' => $journeyStatus,
        ]);

        return [$requester, $donor, $response];
    }
}
