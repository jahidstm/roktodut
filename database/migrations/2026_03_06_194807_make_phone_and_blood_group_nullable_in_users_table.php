<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable()->change();
        $table->string('blood_group')->nullable()->change();
        
        // সেফটির জন্য এগুলোকেও nullable করে দেওয়া ভালো
        if (Schema::hasColumn('users', 'division')) {
            $table->string('division')->nullable()->change();
        }
        if (Schema::hasColumn('users', 'upazila')) {
            $table->string('upazila')->nullable()->change();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
