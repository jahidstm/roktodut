<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('email');
            $table->string('provider_id')->nullable()->after('provider');
            $table->boolean('is_onboarded')->default(false)->after('provider_id');
            // সোশ্যাল লগইনে পাসওয়ার্ড থাকে না, তাই nullable করছি
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'is_onboarded']);
            $table->string('password')->nullable(false)->change();
        });
    }
};