<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phone_reveal_logs', function (Blueprint $table) {
            $table->id();
            // নতুন কলামগুলো
            $table->foreignId('blood_request_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('viewer_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('revealed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_reveal_logs');
    }
};