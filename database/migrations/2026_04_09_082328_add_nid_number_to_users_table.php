<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // NID নাম্বার স্টোর করার জন্য — nullable কারণ সব ইউজার এখনই দেবে না
            $table->string('nid_number', 20)->nullable()->after('nid_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nid_number');
        });
    }
};
