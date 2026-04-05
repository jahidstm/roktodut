<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ১. Blood Request Responses টেবিলে ভেরিফিকেশন কলাম যোগ করা
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->string('verification_pin', 6)->nullable(); // ৪-৬ ডিজিটের পিন
            $table->string('proof_image_path')->nullable(); // প্রুফ ফটোর পাথ
            $table->enum('verification_status', ['pending', 'claimed', 'verified', 'disputed'])->default('pending');
            $table->timestamp('donor_claimed_at')->nullable(); // ডোনার কখন ক্লেইম করল
        });

        // ২. Users টেবিলে রিওয়ার্ড পয়েন্ট যোগ করা
        Schema::table('users', function (Blueprint $table) {
            $table->integer('reward_points')->default(0);
            $table->integer('total_verified_donations')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropColumn(['verification_pin', 'proof_image_path', 'verification_status', 'donor_claimed_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reward_points', 'total_verified_donations']);
        });
    }
};
