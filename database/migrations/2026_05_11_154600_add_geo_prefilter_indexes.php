<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasIndex('users', 'users_lat_lng_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['latitude', 'longitude'], 'users_lat_lng_idx');
            });
        }

        if (!Schema::hasIndex('users', 'users_match_core_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_donor', 'is_available', 'blood_group', 'district_id'], 'users_match_core_idx');
            });
        }

        if (!Schema::hasIndex('blood_requests', 'blood_requests_lat_lng_idx')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->index(['latitude', 'longitude'], 'blood_requests_lat_lng_idx');
            });
        }

        if (!Schema::hasIndex('blood_requests', 'blood_requests_match_core_idx')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->index(['status', 'is_hidden', 'blood_group', 'component_type', 'district_id'], 'blood_requests_match_core_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_match_core_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_match_core_idx');
            });
        }

        if (Schema::hasIndex('users', 'users_lat_lng_idx')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_lat_lng_idx');
            });
        }

        if (Schema::hasIndex('blood_requests', 'blood_requests_match_core_idx')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->dropIndex('blood_requests_match_core_idx');
            });
        }

        if (Schema::hasIndex('blood_requests', 'blood_requests_lat_lng_idx')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->dropIndex('blood_requests_lat_lng_idx');
            });
        }
    }
};
