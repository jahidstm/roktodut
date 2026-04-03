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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();

            // ফর্ম ভ্যালিডেশনের সাথে সিঙ্ক করে Nullable করা হলো
            $table->string('patient_name')->nullable();
            $table->string('blood_group');
            $table->integer('bags_needed')->default(1);
            $table->string('hospital')->nullable();

            // 📍 নতুন রিলেশনাল লোকেশন আইডি (The Core Fix)
            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('upazila_id')->constrained('upazilas');

            $table->text('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_number', 30); // 30 করা হলো (ভ্যালিডেশনের সাথে মিল রেখে)
            $table->string('urgency')->default(UrgencyLevel::NORMAL->value);

            // ⏰ Date থেকে DateTime-এ পরিবর্তন (যাতে সময়টাও সেভ হয়)
            $table->dateTime('needed_at')->nullable();

            $table->string('status')->default('pending'); // pending, in_progress, fulfilled, expired
            $table->text('notes')->nullable();
            $table->timestamps();

            // পারফরম্যান্সের জন্য ইনডেক্সিং (সার্চিং ফাস্ট করার জন্য district_id তে ইনডেক্স)
            $table->index('blood_group');
            $table->index('district_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
