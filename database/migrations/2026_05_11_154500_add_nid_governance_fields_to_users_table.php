<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'nid_number_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nid_number_hash', 64)->nullable()->after('nid_number');
            });
        }

        if (!Schema::hasColumn('users', 'nid_uploaded_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('nid_uploaded_at')->nullable()->after('nid_path');
            });
        }

        if (!Schema::hasColumn('users', 'nid_retention_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('nid_retention_until')->nullable()->after('nid_uploaded_at');
            });
        }

        if (!Schema::hasColumn('users', 'nid_last_accessed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('nid_last_accessed_at')->nullable()->after('nid_retention_until');
            });
        }

        if (!Schema::hasIndex('users', 'users_nid_retention_until_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('nid_retention_until', 'users_nid_retention_until_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_nid_retention_until_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_nid_retention_until_idx');
            });
        }

        if (Schema::hasColumn('users', 'nid_last_accessed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nid_last_accessed_at');
            });
        }

        if (Schema::hasColumn('users', 'nid_retention_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nid_retention_until');
            });
        }

        if (Schema::hasColumn('users', 'nid_uploaded_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nid_uploaded_at');
            });
        }

        if (Schema::hasColumn('users', 'nid_number_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nid_number_hash');
            });
        }
    }
};
