<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DB Patch: Add view_count to posts table.
 *
 * Provides the persistent column that PostViewCacheService::flushToDatabase()
 * writes to when flushing Redis counters via `php artisan blog:flush-views`.
 *
 * Design notes:
 *  • unsignedBigInteger  — never negative; supports viral posts (>4B views).
 *  • default(0)          — safe starting value; Redis delta is additive.
 *  • after('published_at') — keeps column order logical in schema dumps.
 *  • index()             — enables ORDER BY view_count (trending feeds).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')
                  ->default(0)
                  ->after('published_at')
                  ->comment('Persistent view count; incremented in batch from Redis by blog:flush-views.');

            // Index allows efficient "trending posts" queries:
            //   Post::published()->orderByDesc('view_count')->limit(10)->get()
            $table->index('view_count', 'posts_view_count_idx');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_view_count_idx');
            $table->dropColumn('view_count');
        });
    }
};
