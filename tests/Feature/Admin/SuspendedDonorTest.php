<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuspendedDonorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_medical_clearance_document()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $path = $file->store('medical_clearances', 'local');

        $donor = User::factory()->create([
            'role' => UserRole::DONOR->value,
            'is_donor' => false,
            'suspension_reason' => 'medical_issue',
            'medical_clearance_document' => $path,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/medical-documents/{$donor->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_unauthorized_user_cannot_download_medical_clearance_document()
    {
        Storage::fake('local');
        $path = 'medical_clearances/fake.pdf';

        $donor = User::factory()->create([
            'role' => UserRole::DONOR->value,
            'medical_clearance_document' => $path,
        ]);

        $regularUser = User::factory()->create([
            'role' => UserRole::DONOR->value,
        ]);

        $response = $this->actingAs($regularUser)
            ->get("/admin/medical-documents/{$donor->id}");

        // Admin middleware should block it
        $response->assertStatus(403);
    }

    public function test_admin_can_reactivate_suspended_donor_and_delete_document()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $path = $file->store('medical_clearances', 'local');

        $donor = User::factory()->create([
            'role' => UserRole::DONOR->value,
            'is_donor' => false,
            'suspension_reason' => 'medical_issue',
            'medical_clearance_document' => $path,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/medical-documents/{$donor->id}/reactivate");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $donor->refresh();

        $this->assertEquals(1, $donor->is_donor);
        $this->assertNull($donor->suspension_reason);
        $this->assertNull($donor->medical_clearance_document);
        Storage::disk('local')->assertMissing($path);
    }
}
