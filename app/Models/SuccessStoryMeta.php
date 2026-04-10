<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SuccessStoryMeta — One-to-One companion to Post (type = 'story')
 *
 * Stores blood-donation story metadata including geographic context,
 * the linked donation record, verification status, and patient anonymization.
 *
 * Schema reference: migration 2026_04_10_000002_create_success_story_meta_table
 *
 * @property int         $id
 * @property int         $post_id
 * @property string|null $district            Geographic origin of the story
 * @property string|null $donation_ref_type   Polymorphic type (e.g. App\Models\Donation)
 * @property int|null    $donation_ref_id     FK to the actual donation record
 * @property bool        $is_verified_story   Admin-confirmed as genuine
 * @property string      $anonymize_level     'public' | 'initials' | 'anonymous'
 */
class SuccessStoryMeta extends Model
{
    protected $table = 'success_story_meta';

    protected $fillable = [
        'post_id',
        'district',
        'donation_ref_type',
        'donation_ref_id',
        'is_verified_story',
        'anonymize_level',
    ];

    protected function casts(): array
    {
        return [
            'is_verified_story' => 'boolean',
        ];
    }

    // =========================================================================
    // ── Relationships ─────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * The parent post that owns this meta record.
     * Inverse of Post::storyMeta() (HasOne → BelongsTo).
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // =========================================================================
    // ── Helper Methods ────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Whether any anonymization is applied (i.e. not fully public).
     * Maps to the DB enum: 'public' | 'initials' | 'anonymous'
     */
    public function shouldAnonymize(): bool
    {
        return $this->anonymize_level !== 'public';
    }

    /**
     * Whether to show only the author's initials (e.g. "M. H.").
     */
    public function isInitialsOnly(): bool
    {
        return $this->anonymize_level === 'initials';
    }

    /**
     * Whether the author must be completely hidden (displayed as "একজন রক্তদাতা").
     */
    public function isFullyAnonymized(): bool
    {
        return $this->anonymize_level === 'anonymous';
    }
}
