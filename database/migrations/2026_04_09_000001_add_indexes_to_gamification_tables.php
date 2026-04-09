<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance Optimization Migration
 *
 * লিডারবোর্ড কোয়েরি ফাস্ট করার জন্য Composite Index যোগ।
 *
 * users টেবিল:
 *   - (total_verified_donations, points) — all-time লিডারবোর্ড ORDER BY দুটো
 *     কলামেই হয়, কম্পোজিট ইনডেক্স ফুল টেবিল স্ক্যান এড়ায়।
 *   - (monthly_points_month, monthly_points) — monthly লিডারবোর্ড WHERE +
 *     ORDER BY কে কভার করে।
 *
 * point_logs টেবিল:
 *   - (user_id, created_at) — ইতিমধ্যে create_point_logs_table মাইগ্রেশনে
 *     ঘোষণা করা আছে। এখানে শুধু নিশ্চিতের জন্য এবং ভবিষ্যতে যদি টেবিল
 *     রি-ক্রিয়েট হয়, তার জন্য এটি আলাদা মাইগ্রেশনেও ধরে রাখা হলো।
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── users টেবিলে Composite Index ────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {

            // All-time লিডারবোর্ড:
            // ORDER BY total_verified_donations DESC, points DESC
            $table->index(
                ['total_verified_donations', 'points'],
                'idx_users_leaderboard_alltime'
            );

            // Monthly লিডারবোর্ড:
            // WHERE monthly_points_month = ? ORDER BY monthly_points DESC
            $table->index(
                ['monthly_points_month', 'monthly_points'],
                'idx_users_leaderboard_monthly'
            );
        });

        // ── point_logs টেবিলে Composite Index (idempotent guard) ────────────
        // মূল create_point_logs_table মাইগ্রেশনে এটি আগেই ছিল।
        // নিচে শুধু যদি কোনো কারণে অনুপস্থিত থাকে তার জন্য conditional guard।
        if (Schema::hasTable('point_logs')) {
            Schema::table('point_logs', function (Blueprint $table) {
                // (user_id, created_at) index — monthly aggregation কোয়েরির জন্য।
                // DB::select("SHOW INDEX FROM point_logs WHERE Key_name = 'point_logs_user_id_created_at_index'")-
                // এর মতো কনফার্ম না করে এখানে আমরা সরাসরি add করি না,
                // তাই এটি কমেন্ট রইল। নতুন পরিবেশে যদি index না থাকে নিচের
                // লাইনটি uncomment করুন:
                //
                // $table->index(['user_id', 'created_at'], 'idx_point_logs_user_period');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_leaderboard_alltime');
            $table->dropIndex('idx_users_leaderboard_monthly');
        });
    }
};
