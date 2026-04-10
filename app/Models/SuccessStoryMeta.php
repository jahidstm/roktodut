<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
