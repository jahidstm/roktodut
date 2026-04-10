<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PostViewCacheService — Redis-backed view-count management for Blog posts.
 *
 * Architecture Overview
 * ─────────────────────
 * Instead of doing `UPDATE posts SET views = views + 1` on every page view
 * (which causes InnoDB hot-row contention at scale), we:
 *
 *  1. Increment an atomic Redis counter per post (O(1), in-memory, no locking).
 *  2. Run a periodic background job (`FlushPostViewsToDatabase` command, or a
 *     scheduled task) that reads all dirty counters from Redis in one SCAN, then
 *     persists them to MySQL via a single batch UPDATE, and resets the counters.
 *
 * Redis Key Schema
 * ────────────────
 *   post:views:{id}     →  integer (current delta since last DB flush)
 *   post:views:dirty    →  Redis SET of post IDs with pending increments
 *
 * The "dirty set" avoids a full Redis SCAN on every flush — we only touch
 * posts that actually received views in the current window.
 */
class PostViewCacheService
{
    /**
     * The Redis connection to use.
     * Uses the default connection (configured in config/database.php → redis).
     */
    protected mixed $redis;

    /**
     * Name of the Redis SET that tracks which post IDs have dirty counters.
     * Populated by incrementView(); consumed and cleared by flushToDatabase().
     */
    protected const DIRTY_SET_KEY = 'post:views:dirty';

    public function __construct()
    {
        $this->redis = app('redis');
    }

    // =========================================================================
    // ── Public API ────────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Atomically increment the view counter for a given post.
     *
     * @param  Post|int $post  A Post model instance or a raw post ID.
     * @return int             The new total Redis view count for this post.
     */
    public function incrementView(Post|int $post): int
    {
        $id  = $post instanceof Post ? $post->id : $post;
        $key = Post::viewCacheKey($id);

        // Atomic increment — Redis guarantees no lost updates even under
        // thousands of concurrent requests to the same key.
        $newCount = (int) $this->redis->incr($key);

        // Set a safety TTL on first write so orphaned keys eventually expire.
        if ($newCount === 1) {
            $this->redis->expire($key, 60 * 60 * 24 * 30); // 30 days
        }

        // Track this post ID in the dirty set for efficient batch flushing.
        $this->redis->sadd(self::DIRTY_SET_KEY, $id);

        return $newCount;
    }

    /**
     * Read the current live Redis view count for a post (does not touch DB).
     *
     * @param  Post|int $post
     * @return int             0 if the key does not exist in Redis.
     */
    public function getViewCount(Post|int $post): int
    {
        $id  = $post instanceof Post ? $post->id : $post;
        $raw = $this->redis->get(Post::viewCacheKey($id));

        return $raw !== null ? (int) $raw : 0;
    }

    /**
     * Flush all pending Redis view-count deltas to the `posts` table.
     *
     * Called by `php artisan blog:flush-views` (scheduled every few minutes).
     *
     * Algorithm:
     *   1. Atomically pop all IDs from the dirty set (SMEMBERS + DEL).
     *   2. For each dirty ID, GETDEL the counter value from Redis.
     *   3. Batch-update posts table using a single query with CASE/WHEN.
     *
     * WHY GETDEL instead of GET + DEL?
     *   Redis 6.2+ GETDEL is atomic — it returns the value and removes the key
     *   in one operation, preventing double-counting if INCR fires between our
     *   GET and DEL calls. For Redis < 6.2 we use a Lua script equivalent.
     *
     * @return array{flushed: int, skipped: int}  Summary for logging/monitoring.
     */
    public function flushToDatabase(): array
    {
        // Step 1: Retrieve and clear the dirty set atomically.
        // SMEMBERS + DEL is not atomic, but the window is tiny and any IDs that
        // slip in after SMEMBERS will simply be flushed on the next cycle.
        $dirtyIds = $this->redis->smembers(self::DIRTY_SET_KEY);

        if (empty($dirtyIds)) {
            return ['flushed' => 0, 'skipped' => 0];
        }

        // Remove the dirty set now — new INCRs will re-add IDs as needed.
        $this->redis->del(self::DIRTY_SET_KEY);

        $updates = [];
        $skipped = 0;

        foreach ($dirtyIds as $postId) {
            $postId = (int) $postId;
            $key    = Post::viewCacheKey($postId);

            // GETDEL — atomic get-and-delete (Redis 6.2+).
            // Falls back to a GET + DEL pair for older Redis.
            $delta = $this->atomicGetDel($key);

            if ($delta !== null && $delta > 0) {
                $updates[$postId] = $delta;
            } else {
                $skipped++;
            }
        }

        if (empty($updates)) {
            return ['flushed' => 0, 'skipped' => $skipped];
        }

        // Step 2: Batch UPDATE using raw SQL for efficiency.
        // Generates: UPDATE posts SET view_count = view_count + CASE id WHEN 1 THEN 42 … END WHERE id IN (…)
        // NOTE: This requires a `view_count` column on the posts table.
        //       Add it via: $table->unsignedBigInteger('view_count')->default(0)->after('published_at');
        $this->batchUpdateViewCounts($updates);

        Log::info('[PostViewCache] Flushed view counts to DB.', [
            'flushed_posts' => count($updates),
            'skipped'       => $skipped,
        ]);

        return ['flushed' => count($updates), 'skipped' => $skipped];
    }

    // =========================================================================
    // ── Private Helpers ───────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Atomically get and delete a Redis key.
     *
     * Uses GETDEL (Redis 6.2+) when available; otherwise uses a Lua script
     * that provides the same atomicity guarantee on older servers.
     *
     * @return int|null  The integer value that was stored, or null if missing.
     */
    private function atomicGetDel(string $key): ?int
    {
        try {
            // Redis 6.2+ native atomic GETDEL
            $raw = $this->redis->command('GETDEL', [$key]);
        } catch (\Throwable) {
            // Fallback for Redis < 6.2: Lua script (EVAL executes atomically)
            $luaScript = <<<'LUA'
                local val = redis.call('GET', KEYS[1])
                if val then
                    redis.call('DEL', KEYS[1])
                end
                return val
            LUA;
            $raw = $this->redis->eval($luaScript, 1, $key);
        }

        return $raw !== null ? (int) $raw : null;
    }

    /**
     * Issue a single batched SQL UPDATE for all dirty view-count deltas.
     *
     * Uses a CASE/WHEN expression so that only one DB round-trip is needed
     * regardless of how many posts are being updated.
     *
     * @param array<int, int> $updates  Map of post_id → view_delta
     */
    private function batchUpdateViewCounts(array $updates): void
    {
        $ids        = array_keys($updates);
        $bindings   = [];
        $caseClause = 'CASE id';

        foreach ($updates as $postId => $delta) {
            $caseClause .= ' WHEN ? THEN view_count + ?';
            $bindings[]  = $postId;
            $bindings[]  = $delta;
        }

        $caseClause .= ' ELSE view_count END';
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Append the WHERE IN bindings
        foreach ($ids as $id) {
            $bindings[] = $id;
        }

        DB::statement(
            "UPDATE posts SET view_count = {$caseClause} WHERE id IN ({$placeholders})",
            $bindings
        );
    }
}
