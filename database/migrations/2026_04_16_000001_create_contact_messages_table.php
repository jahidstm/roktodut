<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * যোগাযোগ বার্তা সংরক্ষণের টেবিল।
     * ফিচার: স্ট্যাটাস ট্র্যাকিং, স্প্যাম ম্যানেজমেন্ট, IP/UA লগিং
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // ─── প্রেরক তথ্য ────────────────────────────────────────────
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('লগইন করা ইউজার হলে তার ID; গেস্টের ক্ষেত্রে null');

            $table->string('name', 120)->nullable()
                  ->comment('গেস্ট প্রেরকের নাম (লগইন ইউজার হলে users টেবিল থেকে নেওয়া হবে)');

            $table->string('email', 180)
                  ->comment('প্রেরকের ইমেইল');

            $table->string('phone', 20)->nullable()
                  ->comment('ঐচ্ছিক ফোন নম্বর');

            // ─── বার্তার বিষয়বস্তু ──────────────────────────────────────
            $table->string('subject', 120)
                  ->comment('বার্তার বিষয় (৫-১২০ অক্ষর)');

            $table->text('message')
                  ->comment('মূল বার্তা (২০-২০০০ অক্ষর)');

            // ─── স্ট্যাটাস ───────────────────────────────────────────────
            $table->enum('status', ['new', 'in_progress', 'resolved', 'spam'])
                  ->default('new')
                  ->comment('বার্তার বর্তমান অবস্থা');

            // ─── নিরাপত্তা মেটাডেটা ──────────────────────────────────────
            $table->string('ip_address', 45)->nullable()
                  ->comment('প্রেরকের IP ঠিকানা (IPv4/IPv6)');

            $table->string('user_agent', 500)->nullable()
                  ->comment('প্রেরকের ব্রাউজার/ডিভাইস তথ্য');

            $table->timestamps();

            // ─── ইনডেক্সসমূহ ────────────────────────────────────────────
            $table->index('status');
            $table->index('created_at');
            $table->index('email');
            $table->index('user_id');
            $table->index(['status', 'created_at']); // অ্যাডমিন লিস্ট কোয়েরির জন্য কম্পোজিট
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
