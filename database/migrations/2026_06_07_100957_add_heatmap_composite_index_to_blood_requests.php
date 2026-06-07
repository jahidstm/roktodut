<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a composite index (district_id, status, blood_group) to blood_requests.
 *
 * হিটম্যাপ কোয়েরি এই তিনটি কলাম একসাথে ফিল্টার ও গ্রুপ করে:
 *   WHERE status IN ('pending', 'in_progress')
 *   JOIN districts ON district_id = districts.id
 *   GROUP BY district_name, blood_group
 *
 * Composite index দিলে MySQL Index-Only Scan ব্যবহার করতে পারে,
 * ফলে বড় ডেটাসেটেও হিটম্যাপ লোড দ্রুত হবে।
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // Composite index: heatmap query optimization
            // Covers: WHERE status IN (...) + GROUP BY district_id, blood_group
            $table->index(
                ['district_id', 'status', 'blood_group'],
                'idx_br_heatmap'
            );
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropIndex('idx_br_heatmap');
        });
    }
};
