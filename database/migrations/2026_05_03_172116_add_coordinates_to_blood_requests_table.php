<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 📍 Geospatial Upgrade — blood_requests table
 *
 * Adds lat/lng to blood_requests so we can perform radius-based
 * donor matching (Haversine formula) instead of district-only matching.
 *
 * Also adds a composite index on (latitude, longitude) for faster
 * spatial queries and an index on district_id for the fallback path.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // 📍 Hospital / pickup-point coordinates (optional — populated via Leaflet map)
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            // ⚡ Composite index for Haversine queries
            $table->index(['latitude', 'longitude'], 'blood_requests_latlon_index');
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropIndex('blood_requests_latlon_index');
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
