<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneRevealLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'viewer_user_id',
        'donor_user_id',
        'revealed_at',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'revealed_at' => 'datetime',
    ];

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_user_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_user_id');
    }
}