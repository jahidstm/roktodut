<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // ডোনারের ফোন নম্বর পাবলিক ফিডে গোপন রাখার ফ্ল্যাগ
            // true হলে: ফিডে নম্বর দেখাবে না, "রক্ত দিতে চাই" বাটন দেখাবে
            // false হলে: পূর্বের মতো নম্বর দেখাবে
            $table->boolean('is_phone_hidden')->default(false)->after('contact_number');
        });

        Schema::table('blood_request_responses', function (Blueprint $table) {
            // 'contacted' স্ট্যাটাস: ডোনার পিং পাঠিয়েছে, রোগী Telegram এ নম্বর পেয়েছে
            // অ্যানালিটিক্সের জন্য tracked হবে
            $table->boolean('is_ping_sent')->default(false)->after('status');
            $table->timestamp('pinged_at')->nullable()->after('is_ping_sent');
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn('is_phone_hidden');
        });

        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropColumn(['is_ping_sent', 'pinged_at']);
        });
    }
};
