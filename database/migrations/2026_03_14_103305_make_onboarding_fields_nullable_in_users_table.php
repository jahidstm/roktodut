<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // রেজিস্ট্রেশন স্মুথ করতে বাকি সব ফিল্ড nullable করে দেওয়া হলো
            if (Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'blood_group')) {
                $table->string('blood_group')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'thana')) {
                $table->string('thana')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'upazila')) {
                $table->string('upazila')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable(false)->change();
            }
            if (Schema::hasColumn('users', 'blood_group')) {
                $table->string('blood_group')->nullable(false)->change();
            }
        });
    }
};