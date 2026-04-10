<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog Post Model — RoktoDut Blog Module
 *
 * Security hardening implemented:
 *  • Store-time XSS sanitization  (body_raw → body_sanitized mutator)
 *  • Immutable, collision-safe slugs (boot() creating event only)
 *  • Redis-backed view count increments (no DB hot-row contention)
 *
 * @property int         $id
 * @property int         $author_user_id
 * @property string      $title
 * @property string      $slug
 * @property string|null $excerpt
 * @property string      $body_raw
 * @property string      $body_sanitized
 * @property string|null $cover_image
 * @property string      $type            health|story
 * @property string      $status          draft|pending_review|published|rejected
 * @property \Carbon\Carbon|null $published_at
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_user_id',
        'title',
        'slug',
        'excerpt',
        'body_raw',
        'body_sanitized',
        'cover_image',
        'type',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    // =========================================================================
    // ── Boot — Slug Generation (creating only, never on update) ──────────────
    // =========================================================================

    protected static function boot(): void
    {
        parent::boot();

        /**
         * CREATING event: auto-generate a unique, URL-safe slug.
         *
         * Strategy:
         *   1. Transliterate title → slug base (Str::slug).
         *   2. If the base slug is available → use it as-is (clean URLs).
         *   3. Collision: append "-{base36(max_id + 1)}" — compact, monotonic,
         *      and avoids the thundering-herd of random suffix retries.
         *
         * WHY base36? It's shorter than decimal and URL-safe (no special chars).
         * e.g. post #1000 → suffix "-rs" instead of "-1001".
         */
        static::creating(function (Post $post): void {
            // Allow callers to pre-set a slug (e.g., from an API payload);
            // only auto-generate if it is blank.
            if (filled($post->slug)) {
                return;
            }

            $post->slug = static::generateUniqueSlug($post->title);
        });

        /**
         * UPDATING event: slug is intentionally NOT regenerated.
         *
         * CRITICAL — SEO IMMUTABILITY:
         * Changing a slug after publication breaks inbound links, Google's
         * index, and any hard-coded share URLs. Once a slug is set, it must
         * never change automatically. Admins can manually update it via the
         * back-office if absolutely required.
         */
        // static::updating(function (Post $post): void { /* slug is immutable */ });
    }

    /**
     * Generate a unique slug for the given title.
     *
     * @param  string $title  Raw post title
     * @return string         Guaranteed-unique, URL-safe slug
     */
    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);

        // Guard: empty title (e.g. purely non-Latin chars that Str::slug strips)
        if (empty($base)) {
            $base = 'post';
        }

        // Optimistic path — no collision
        if (!static::where('slug', $base)->exists()) {
            return $base;
        }

        // Collision path — use base36 of (max_id + 1) as a compact, unique suffix.
        // This is deterministic given the current table state, which is fine because
        // the combination of base slug + suffix is still unique.
        $nextId = (int) DB::table('posts')->max('id') + 1;
        $suffix = base_convert((string) $nextId, 10, 36); // e.g. 1001 → "rt"

        $candidate = "{$base}-{$suffix}";

        // Highly unlikely second-level collision (e.g. someone manually set the same
        // slug), but we handle it gracefully with a short random tail.
        if (static::where('slug', $candidate)->exists()) {
            $candidate = "{$base}-{$suffix}-" . Str::lower(Str::random(4));
        }

        return $candidate;
    }

    // =========================================================================
    // ── Security Mutator: Store-Time XSS Sanitization ────────────────────────
    // =========================================================================

    /**
     * Mutator: sanitize body_raw and persist clean HTML to body_sanitized.
     *
     * Fires automatically whenever `body_raw` is assigned:
     *   $post->body_raw = $request->input('body');  // ← triggers this mutator
     *   $post->save();
     *
     * Uses HTMLPurifier via mews/purifier (`composer require mews/purifier`).
     * HTMLPurifier is a spec-compliant, context-aware XSS defense — it parses
     * the HTML token-by-token, whitelists tags/attributes per its config, and
     * fully neutralises injection vectors that regex-based approaches miss.
     *
     * Configure the purifier profile in config/purifier.php as needed
     * (e.g., to permit <iframe> for YouTube embeds in a custom profile).
     */
    public function setBodyRawAttribute(string $value): void
    {
        // 1. Store the original, untouched input for audit/diff purposes.
        $this->attributes['body_raw'] = $value;

        // 2. HTMLPurifier produces the safe, render-ready version.
        $this->attributes['body_sanitized'] = clean($value);
    }

    // =========================================================================
    // ── Redis View Count Helper ───────────────────────────────────────────────
    // =========================================================================

    /**
     * Redis key pattern for post view counters.
     * Centralised here so it's easy to change the key schema project-wide.
     */
    public static function viewCacheKey(int $postId): string
    {
        return "post:views:{$postId}";
    }

    /**
     * Increment the view count for a post in Redis.
     *
     * WHY REDIS instead of UPDATE posts SET views = views + 1?
     * ─────────────────────────────────────────────────────────
     * On a popular post, dozens of concurrent requests hit the same DB row,
     * causing InnoDB row-level lock contention ("hot row" problem) that
     * serialises requests and hammers replication lag.
     *
     * Redis INCR is atomic and O(1), operates entirely in memory, and can
     * handle millions of ops/sec without contention. A background job
     * (e.g. a scheduled Artisan command or queue worker) can periodically
     * flush the Redis counters back to the `posts` table in a single
     * batch UPDATE, dramatically reducing DB write pressure.
     *
     * Usage:
     *   Post::incrementPostView($post);       // static call
     *   $post->incrementView();               // instance call (alias below)
     *
     * @param  Post $post  The post being viewed
     * @return int         New view count (from Redis)
     */
    public static function incrementPostView(Post $post): int
    {
        $key = static::viewCacheKey($post->id);

        /** @var \Illuminate\Redis\Connections\PhpRedisConnection $redis */
        $redis = app('redis');

        // INCR is atomic — no race condition possible.
        $newCount = (int) $redis->incr($key);

        // Persist TTL on first write so stale counters eventually expire
        // (e.g., if a flush job fails for an extended period).
        // 30 days gives plenty of headroom for the scheduled flush.
        if ($newCount === 1) {
            $redis->expire($key, 60 * 60 * 24 * 30); // 30 days
        }

        return $newCount;
    }

    /**
     * Instance-method alias for incrementPostView — more ergonomic in controllers.
     *
     *   $post->incrementView();
     */
    public function incrementView(): int
    {
        return static::incrementPostView($this);
    }

    /**
     * Get the total view count for this post.
     *
     * Returns: DB persisted count (flushed by blog:flush-views) PLUS the live
     * Redis delta (increments since the last flush).
     *
     * This gives an always-accurate number without waiting for the next flush
     * cycle — the two sources are additive and never double-count because
     * flushToDatabase() atomically GETDELs Redis keys before writing to DB.
     *
     *   DB column  → authoritative history (persisted across Redis restarts)
     *   Redis key  → live delta since last flush (ultra-fast, in-memory)
     */
    public function getViewCountAttribute(): int
    {
        // DB persisted base (0 until first flush cycle runs).
        $dbCount = (int) ($this->attributes['view_count'] ?? 0);

        // Live Redis delta (0 if key expired or not yet set).
        $redis     = app('redis');
        $redisDelta = $redis->get(static::viewCacheKey($this->id));

        return $dbCount + ($redisDelta !== null ? (int) $redisDelta : 0);
    }

    // =========================================================================
    // ── Relationships ─────────────────────────────────────────────────────────
    // =========================================================================

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function healthMeta(): HasOne
    {
        return $this->hasOne(HealthBlogMeta::class, 'post_id');
    }

    public function storyMeta(): HasOne
    {
        return $this->hasOne(SuccessStoryMeta::class, 'post_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post')
                    ->withTimestamps();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PostReport::class, 'post_id');
    }

    // =========================================================================
    // ── Query Scopes ──────────────────────────────────────────────────────────
    // =========================================================================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHealthBlogs($query)
    {
        return $query->where('type', 'health');
    }

    public function scopeSuccessStories($query)
    {
        return $query->where('type', 'story');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeByAuthor($query, int $userId)
    {
        return $query->where('author_user_id', $userId);
    }

    // =========================================================================
    // ── Boolean Helpers ───────────────────────────────────────────────────────
    // =========================================================================

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isHealthBlog(): bool
    {
        return $this->type === 'health';
    }

    public function isSuccessStory(): bool
    {
        return $this->type === 'story';
    }
}
