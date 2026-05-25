<?php

namespace Tests\Feature\Jobs;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Jobs\SmartBloodRequestBroadcastJob;
use App\Models\BloodRequest;
use App\Models\User;
use App\Services\DfiCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SmartBloodRequestBroadcastJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_fetching_avoids_n_plus_one_queries()
    {
        $division = \App\Models\Division::create(['name' => 'Dhaka']);
        $district = \App\Models\District::create(['division_id' => $division->id, 'name' => 'Dhaka']);
        $upazila = \App\Models\Upazila::create(['district_id' => $district->id, 'name' => 'Dhaka']);
        $requester = User::factory()->create();

        // Create 10 donors matching a blood group
        User::factory()->count(10)->create([
            'role' => UserRole::DONOR->value,
            'is_donor' => true,
            'blood_group' => BloodGroup::A_POS->value,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'upazila_id' => $upazila->id,
            'is_shadowbanned' => false,
            'is_available' => true,
        ]);

        $bloodRequest = BloodRequest::create([
            'requested_by' => $requester->id,
            'patient_name' => 'Test Patient',
            'blood_group' => BloodGroup::A_POS->value,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'upazila_id' => $upazila->id,
            'is_super_critical' => false,
            'status' => 'pending',
            'contact_number' => '01711111111',
            'location_text' => 'Dhaka Medical',
            'urgency' => 'high',
            'needed_at' => now()->addDays(1),
        ]);

        \Illuminate\Support\Facades\Queue::fake([\App\Jobs\DispatchEmergencyAlertsJob::class]);

        DB::enableQueryLog();

        $job = new SmartBloodRequestBroadcastJob($bloodRequest->id);
        $job->handle(app(DfiCalculationService::class));

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // We expect a constant number of queries:
        // 1. Fetch BloodRequest with relationships (district, upazila, hospital)
        // 2. Fetch Candidates
        // 3. Fetch ResponseStats
        // 4. Fetch IgnoredCounts
        // 5-N. Some User DFI updates if any differ (at most 10)
        
        $this->assertLessThan(25, $queryCount, "Too many queries executed, possible N+1 issue. Query count: {$queryCount}");
    }
}
