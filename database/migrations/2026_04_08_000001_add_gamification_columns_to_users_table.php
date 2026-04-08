<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // রেফারেল সিস্টেম
            $table->string('referral_code', 10)->nullable()->unique()->after('points');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();

            // মাসিক পয়েন্ট ট্র্যাকার
            $table->integer('monthly_points')->default(0)->after('referred_by');
            $table->string('monthly_points_month', 7)->nullable()->after('monthly_points'); // YYYY-MM ফরম্যাটে

            // ক্যাম্পাস হিরো ট্র্যাক
            $table->boolean('is_campus_hero')->default(false)->after('monthly_points_month');
        });

        // Badges টেবিলে কিছু নতুন কলাম
        Schema::table('badges', function (Blueprint $table) {
            $table->string('color')->default('#dc2626')->after('icon'); // hex color
            $table->integer('points_requirement')->default(0)->after('requirement');
            $table->string('emoji')->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'referral_code', 'referred_by', 'monthly_points',
                'monthly_points_month', 'is_campus_hero',
            ]);
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['color', 'points_requirement', 'emoji']);
        });
    }
};
