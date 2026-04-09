<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shadowban Migration
 *
 * is_shadowbanned = true হলে ইউজার লিডারবোর্ডে থেকেও র‌্যাঙ্ক দেখতে পাবে না
 * এবং লিডারবোর্ড কোয়েরি থেকে সে বাদ পড়বে — তবে তার অ্যাকাউন্ট সক্রিয় থাকবে।
 *
 * এটি ফেক ডোনার বা স্প্যামারদের র‌্যাঙ্ক ম্যানিপুলেশন থেকে বিরত রাখার জন্য।
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_shadowbanned')
                ->default(false)
                ->after('is_campus_hero')
                ->comment('Shadowbanned users are hidden from all leaderboards without notification.');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_shadowbanned');
        });
    }
};
