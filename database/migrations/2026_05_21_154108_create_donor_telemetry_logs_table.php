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
        Schema::create('donor_telemetry_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blood_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('notification_type')->default('fcm_push'); // e.g., fcm_push, sms
            $table->unsignedInteger('latency_ms')->nullable(); // How long before they responded/ignored
            $table->boolean('ignored')->default(true); // Default true until they respond
            $table->decimal('distance_km', 8, 2)->nullable(); // Distance from request
            $table->timestamps();
            
            // Index for fast querying during DFI calculation
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_telemetry_logs');
    }
};
