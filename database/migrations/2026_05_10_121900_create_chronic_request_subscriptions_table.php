<?php

use App\Enums\BloodComponentType;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chronic_request_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('source_blood_request_id')->nullable()->constrained('blood_requests')->nullOnDelete();

            $table->string('patient_name')->nullable();
            $table->string('blood_group');
            $table->string('component_type')->default(BloodComponentType::WHOLE_BLOOD->value);
            $table->unsignedTinyInteger('bags_needed')->default(1);
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();

            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('upazila_id')->constrained('upazilas');

            $table->string('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_number', 30);
            $table->string('contact_number_normalized', 32)->nullable();
            $table->string('urgency')->default(UrgencyLevel::NORMAL->value);
            $table->text('notes')->nullable();
            $table->boolean('is_phone_hidden')->default(false);

            $table->unsignedSmallInteger('cadence_days')->default(28);
            $table->unsignedTinyInteger('lead_time_days')->default(2);
            $table->dateTime('next_needed_at');
            $table->date('last_dispatched_for')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['user_id', 'is_active'], 'crs_user_active_idx');
            $table->index(['district_id', 'blood_group', 'component_type'], 'crs_match_idx');
            $table->index(['is_active', 'next_needed_at'], 'crs_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chronic_request_subscriptions');
    }
};
