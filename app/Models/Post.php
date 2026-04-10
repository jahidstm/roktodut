<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    // ── Relationships ──────────────────────────────────────────────────

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

    // ── Query Scopes ───────────────────────────────────────────────────

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

    // ── Helpers ────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
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
