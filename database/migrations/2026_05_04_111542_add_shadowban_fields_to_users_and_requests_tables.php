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
        if (!Schema::hasColumn('users', 'spam_strikes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('spam_strikes')->default(0);
            });
        }

        if (!Schema::hasColumn('users', 'is_shadowbanned')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_shadowbanned')->default(false);
            });
        }

        if (!Schema::hasColumn('blood_requests', 'spam_report_count')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->integer('spam_report_count')->default(0);
            });
        }

        if (!Schema::hasColumn('blood_requests', 'managed_report_count')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->integer('managed_report_count')->default(0);
            });
        }

        if (!Schema::hasColumn('blood_requests', 'is_hidden')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->boolean('is_hidden')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'spam_strikes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('spam_strikes');
            });
        }

        if (Schema::hasColumn('users', 'is_shadowbanned')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_shadowbanned');
            });
        }

        if (Schema::hasColumn('blood_requests', 'spam_report_count')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->dropColumn('spam_report_count');
            });
        }

        if (Schema::hasColumn('blood_requests', 'managed_report_count')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->dropColumn('managed_report_count');
            });
        }

        if (Schema::hasColumn('blood_requests', 'is_hidden')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->dropColumn('is_hidden');
            });
        }
    }
};
