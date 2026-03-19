<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // ব্লাড গ্রুপের পর নতুন কলামটি যুক্ত হবে
            $table->date('last_donation_date')->nullable()->after('blood_group');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_donation_date');
        });
    }
};