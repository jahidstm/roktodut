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
            // organization_id কলাম তৈরি এবং organizations টেবিলের সাথে ফরেন কি রিলেশনশিপ
            $table->foreignId('organization_id')
                  ->nullable()
                  ->after('role') // role কলামের ঠিক পরে বসবে
                  ->constrained('organizations')
                  ->nullOnDelete(); // অর্গানাইজেশন ডিলিট হলে ইউজারের এই কলাম null হয়ে যাবে
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // রোলব্যাক করার সময় ফরেন কি এবং কলাম রিমুভ করা
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};