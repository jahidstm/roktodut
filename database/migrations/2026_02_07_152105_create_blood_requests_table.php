<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BloodGroup;
use App\Enums\UrgencyLevel;

return new class extends Migration
{
    public function up(): void
    {
        // নিশ্চিত করো এখানে 'blood_requests' লেখা আছে
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('blood_group'); // Enum value জমা হবে
            $table->integer('bags_needed')->default(1);
            $table->string('hospital');
            $table->string('district');
            $table->string('thana')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_name');
            $table->string('contact_number', 15);
            $table->string('urgency')->default(UrgencyLevel::NORMAL->value);
            $table->date('needed_by')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, fulfilled, expired
            $table->text('notes')->nullable();
            $table->timestamps();

            // পারফরম্যান্সের জন্য ইনডেক্সিং [cite: 2025-10-31]
            $table->index('blood_group');
            $table->index('district');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
