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
 * @property string      $anonymize_level     'none' | 'partial' | 'full'
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
     * Whether the donor/patient name should be redacted in public views.
     */
    public function shouldAnonymize(): bool
    {
        return $this->anonymize_level !== 'none';
    }

    /**
     * Whether to show a partial anonymization (e.g., show "MD. A***" instead of full name).
     */
    public function isPartiallyAnonymized(): bool
    {
        return $this->anonymize_level === 'partial';
    }

    /**
     * Whether the patient/donor must be completely hidden from public view.
     */
    public function isFullyAnonymized(): bool
    {
        return $this->anonymize_level === 'full';
    }
}
