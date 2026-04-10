<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            // Automatically decode/encode the JSON array
            'sources_json' => 'array',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
