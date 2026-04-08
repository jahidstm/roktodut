<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // কত পয়েন্ট দেওয়া হলো (নেগেটিভ হতে পারে ডিডাকশনের ক্ষেত্রে)
            $table->integer('points');

            // কোন কাজে পয়েন্ট পাওয়া গেল
            // যেমন: donation_completed, referral_signup, referral_first_donation,
            //        recipient_review, profile_completion, first_responder_bonus
            $table->string('action_type', 60);

            // অতিরিক্ত মেটাডেটা (যেমন blood_request_id, referral_user_id ইত্যাদি)
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
