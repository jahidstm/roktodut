<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ডোনেশন হিস্ট্রি ট্র্যাকিংয়ের জন্য fulfilled_at যোগ করা।
     * যখন গ্রহীতা ডোনেশন confirm করবে, এই timestamp সেট হবে।
     */
    public function up(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->timestamp('fulfilled_at')->nullable()->after('donor_claimed_at');
        });
    }

    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropColumn('fulfilled_at');
        });
    }
};
