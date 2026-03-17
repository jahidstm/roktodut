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
        Schema::create('blood_request_responses', function (Blueprint $table) {
            $table->id();
            
            // আসল কলামগুলো
            $table->foreignId('blood_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('accepted');
            
            // ডুপ্লিকেট রেসপন্স ঠেকাতে ইউনিক ইনডেক্স
            $table->unique(['blood_request_id', 'user_id'], 'br_responses_request_user_unique');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_request_responses');
    }
};
