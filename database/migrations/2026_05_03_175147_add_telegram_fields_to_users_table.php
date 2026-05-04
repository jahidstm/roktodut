<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // টেলিগ্রাম Chat ID (ডোনার কানেক্ট করলে সেভ হবে)
            $table->string('telegram_chat_id')->nullable()->unique()->after('longitude');

            // ভেরিফিকেশন নিশ্চিত হওয়ার সময়
            $table->timestamp('telegram_connected_at')->nullable()->after('telegram_chat_id');

            // Pending verification token (একবার ব্যবহার করলেই null হয়ে যাবে)
            $table->string('telegram_verify_token', 32)->nullable()->unique()->after('telegram_connected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'telegram_connected_at', 'telegram_verify_token']);
        });
    }
};
