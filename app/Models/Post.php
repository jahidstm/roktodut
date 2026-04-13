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
use Illuminate\Support\Facades\Cache; // 🎯 Cache Facade যুক্ত করা হয়েছে

/**
 * Blog Post Model — RoktoDut Blog Module
 *
 * Security hardening implemented:
 * • Store-time XSS sanitization (body_raw → body_sanitized mutator)
 * • Immutable, collision-safe slugs (boot() creating event only)
 * • Environment-agnostic view count increments (Safe for local/prod)
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

        static::creating(function (Post $post): void {
            if (filled($post->slug)) {
                return;
            }
            $post->slug = static::generateUniqueSlug($post->title);
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        // Limit to first 6 words, then slugify, then limit to 60 chars to avoid excessively long URLs
        $limitedTitle = Str::words($title, 6, '');
        $base = Str::slug($limitedTitle);
        $base = rtrim(Str::limit($base, 60, ''), '-');

        if (empty($base)) {
            $base = 'post';
        }

        if (!static::where('slug', $base)->exists()) {
            return $base;
        }

        $nextId = (int) DB::table('posts')->max('id') + 1;
        $suffix = base_convert((string) $nextId, 10, 36); 

        $candidate = "{$base}-{$suffix}";

        if (static::where('slug', $candidate)->exists()) {
            $candidate = "{$base}-{$suffix}-" . Str::lower(Str::random(4));
        }

        return $candidate;
    }

    // =========================================================================
    // ── Security Mutator: Store-Time XSS Sanitization ────────────────────────
    // =========================================================================

    public function setBodyRawAttribute(string $value): void
    {
        $this->attributes['body_raw'] = $value;
        // HTMLPurifier produces the safe, render-ready version.
        $this->attributes['body_sanitized'] = app('purifier')->clean($value);
    }

    // =========================================================================
    // ── Cache-Based View Count Helper (Redis Compatible) ─────────────────────
    // =========================================================================

    public static function viewCacheKey(int $postId): string
    {
        return "post:views:{$postId}";
    }

    /**
     * Increment the view count for a post using Cache Facade.
     * This is environment-agnostic. It will use Redis on Prod and File/Database locally.
     */
    public static function incrementPostView(Post $post): int
    {
        $key = static::viewCacheKey($post->id);

        // increment() is atomic across most drivers. 
        // If the key doesn't exist, it starts from 1.
        $newCount = Cache::increment($key);

        if ($newCount === 1) {
            // Set TTL on first hit (30 days)
            Cache::put($key, 1, now()->addDays(30));
        }

        return (int) $newCount;
    }

    public function incrementView(): int
    {
        return static::incrementPostView($this);
    }

    /**
     * Accessor: Get combined view count (DB + Cache Delta)
     */
    public function getViewCountAttribute(): int
    {
        $dbCount = (int) ($this->attributes['view_count'] ?? 0);
        
        // Fetch live delta from cache
        $cacheDelta = Cache::get(static::viewCacheKey($this->id), 0);

        return $dbCount + (int) $cacheDelta;
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

    public function scopePublished($query) { return $query->where('status', 'published'); }
    public function scopeHealthBlogs($query) { return $query->where('type', 'health'); }
    public function scopeSuccessStories($query) { return $query->where('type', 'story'); }
    public function scopePendingReview($query) { return $query->where('status', 'pending_review'); }
    public function scopeByAuthor($query, int $userId) { return $query->where('author_user_id', $userId); }

    // =========================================================================
    // ── Boolean Helpers ───────────────────────────────────────────────────────
    // =========================================================================

    public function isPublished(): bool { return $this->status === 'published'; }
    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isPendingReview(): bool { return $this->status === 'pending_review'; }
    public function isHealthBlog(): bool { return $this->type === 'health'; }
    public function isSuccessStory(): bool { return $this->type === 'story'; }
}