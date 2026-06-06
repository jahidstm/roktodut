<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rule type: weekly (recurring), specific_date (one-off), date_range
            $table->enum('type', ['weekly', 'specific_date', 'date_range'])->default('weekly');

            // Bitmask for weekdays — Sun=1 Mon=2 Tue=4 Wed=8 Thu=16 Fri=32 Sat=64
            // Example: শুক্র+শনি = 32+64 = 96 (single integer, 100% index-capable)
            // WHERE (weekdays_bitmask & 32) > 0  ← O(1) Bitwise AND query
            $table->unsignedTinyInteger('weekdays_bitmask')->nullable();

            // Specific date / date range
            $table->date('specific_date')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();

            // Time window (NULL = all day)
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();

            $table->string('note', 200)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index for fast dispatch lookup
            $table->index(['user_id', 'is_active'], 'idx_donor_avail_user_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_availabilities');
    }
};
