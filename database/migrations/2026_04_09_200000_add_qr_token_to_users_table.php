<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ─────────────────────────────────────────────────────────
     * Dynamic QR Smart Card — qr_token কলাম যুক্ত করা
     * ─────────────────────────────────────────────────────────
     * • nullable: NID verify হওয়ার আগে NULL থাকবে।
     * • unique: প্রতিটি ইউজারের token অদ্বিতীয়।
     * • index: token দিয়ে দ্রুত lookup-এর জন্য।
     *
     * Security note:
     *   এই token কোনো ব্যক্তিগত তথ্য (ফোন/ইমেইল/NID) ধারণ করে না।
     *   শুধুমাত্র একটি ক্রিপ্টোগ্রাফিক্যালি র‍্যান্ডম identifier।
     * ─────────────────────────────────────────────────────────
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('qr_token', 64)
                  ->nullable()
                  ->unique()
                  ->after('nid_status')
                  ->comment('NID verified হলে জেনারেট হওয়া QR Smart Card token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
