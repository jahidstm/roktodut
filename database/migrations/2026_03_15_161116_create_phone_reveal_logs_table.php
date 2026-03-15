<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('phone_reveal_logs')) {
            return;
        }

        Schema::create('phone_reveal_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blood_request_id')->constrained('blood_requests')->cascadeOnDelete();
            $table->foreignId('viewer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('donor_user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamp('revealed_at')->useCurrent();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->unique(['blood_request_id', 'viewer_user_id', 'donor_user_id'], 'reveal_unique');
        });
    }

    public function down(): void
    {
        // only drop if exists
        Schema::dropIfExists('phone_reveal_logs');
    }
};