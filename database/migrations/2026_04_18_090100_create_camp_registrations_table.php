<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camp_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camp_id')->constrained('blood_camps')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('registered');
            $table->timestamps();

            $table->unique(['camp_id', 'user_id']);
            $table->index(['camp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camp_registrations');
    }
};
