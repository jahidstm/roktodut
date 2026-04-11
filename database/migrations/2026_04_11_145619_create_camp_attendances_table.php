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
        Schema::create('camp_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_camp_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points_awarded')->default(0);
            $table->timestamps();

            // Prevent duplicate entries for same user at same camp
            $table->unique(['blood_camp_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_attendances');
    }
};
