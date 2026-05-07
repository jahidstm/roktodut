<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_donation_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_phone');
            $table->string('recipient_phone_normalized', 32)->index();
            $table->string('patient_name');
            $table->foreignId('district_id')->constrained('districts');
            $table->string('hospital_name')->nullable();
            $table->date('donation_date');
            $table->string('proof_path')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'admin_review'])->default('pending');
            $table->enum('verification_method', ['recipient', 'admin'])->nullable();
            $table->foreignId('matched_request_id')->nullable()->constrained('blood_requests')->nullOnDelete();
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('risk_score')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->string('ip_hash', 64);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasColumn('users', 'spam_strikes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('spam_strikes')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_donation_claims');
    }
};
