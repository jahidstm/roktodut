<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HealthBlogMeta — One-to-One companion to Post (type = 'health')
 *
 * Stores medical-editorial metadata: the physician or healthcare professional
 * who reviewed the article, plus the source citations used in the content.
 *
 * Schema reference: migration 2026_04_10_000003_create_health_blog_meta_table
 *
 * @property int         $id
 * @property int         $post_id
 * @property int|null    $medically_reviewed_by   FK → users.id (doctor/admin reviewer)
 * @property array|null  $sources_json            JSON array of citation objects
 */
class HealthBlogMeta extends Model
{
    protected $table = 'health_blog_meta';

    protected $fillable = [
        'post_id',
        'medically_reviewed_by',
        'sources_json',
    ];

    protected function casts(): array
    {
        return [
            // Eloquent auto-serialises/deserialises the JSON column to/from a PHP array.
            'sources_json' => 'array',
        ];
    }

    // =========================================================================
    // ── Relationships ─────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * The parent post that owns this meta record.
     * Inverse of Post::healthMeta() (HasOne → BelongsTo).
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * The User (admin/doctor) who medically reviewed this article.
     *
     * This is a soft-reference to the users table — the FK is nullable so
     * a post can exist in draft/pending state before a reviewer is assigned.
     * Note: cascadeOnDelete is intentionally NOT used; if the reviewer's
     * account is deleted we keep the meta intact for audit purposes.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medically_reviewed_by');
    }

    // =========================================================================
    // ── Helper Methods ────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Whether this article has been formally reviewed by a medical professional.
     */
    public function hasReviewer(): bool
    {
        return !is_null($this->medically_reviewed_by);
    }

    /**
     * Whether any source citations have been provided for the article.
     */
    public function hasSources(): bool
    {
        return !empty($this->sources_json);
    }

    /**
     * Returns the count of linked source citations.
     */
    public function sourceCount(): int
    {
        return count($this->sources_json ?? []);
    }
}
