<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor_response_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('blood_requests')->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('notified_at');
            $table->timestamp('responded_at')->nullable();
            $table->enum('status', ['pending', 'accepted', 'ignored', 'declined'])->default('pending');
            $table->integer('response_time_minutes')->nullable();
            $table->decimal('distance_km', 6, 2);
            $table->integer('days_since_last_donation');
            $table->unsignedTinyInteger('temporal_hour');
            $table->boolean('is_weekend')->default(false);
            $table->float('historical_response_rate')->default(0);
            $table->timestamps();

            $table->index(['request_id', 'status'], 'donor_response_logs_request_status_idx');
            $table->index(['donor_id', 'status'], 'donor_response_logs_donor_status_idx');
            $table->index('temporal_hour', 'donor_response_logs_temporal_hour_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_response_logs');
    }
};
