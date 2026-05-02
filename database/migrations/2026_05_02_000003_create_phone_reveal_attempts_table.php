<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_reveal_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_hash', 64);
            $table->enum('status', ['success', 'failed_captcha', 'rate_limited']);
            $table->timestamp('created_at')->useCurrent();

            $table->index('donor_id');
            $table->index('ip_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_reveal_attempts');
    }
};
