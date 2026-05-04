<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blood_request_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // The reporter
            $table->foreignId('blood_request_id')->constrained()->cascadeOnDelete();
            $table->enum('reason', ['fake_number', 'already_managed', 'spam', 'abusive']);
            $table->enum('status', ['pending', 'strike_approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Prevent duplicate reports from the same user on the same request
            $table->unique(['user_id', 'blood_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_request_reports');
    }
};
