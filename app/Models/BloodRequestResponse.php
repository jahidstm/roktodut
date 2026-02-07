<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequestResponse extends Model
{
    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'message',
    ];

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
}
