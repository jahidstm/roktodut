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
            $table->integer('spam_strikes')->default(0);
            $table->boolean('is_shadowbanned')->default(false);
        });

        Schema::table('blood_requests', function (Blueprint $table) {
            $table->integer('spam_report_count')->default(0);
            $table->integer('managed_report_count')->default(0);
            $table->boolean('is_hidden')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['spam_strikes', 'is_shadowbanned']);
        });

        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn(['spam_report_count', 'managed_report_count', 'is_hidden']);
        });
    }
};
