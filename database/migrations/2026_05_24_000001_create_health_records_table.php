<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('systolic_bp')->nullable();
            $table->unsignedSmallInteger('diastolic_bp')->nullable();
            $table->decimal('hemoglobin_level', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->dateTime('recorded_at');
            $table->string('source')->default('self_reported');
            $table->timestamps();

            $table->index('user_id');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
