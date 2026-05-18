<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorResponseLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'donor_id',
        'notified_at',
        'responded_at',
        'status',
        'response_time_minutes',
        'distance_km',
        'days_since_last_donation',
        'temporal_hour',
        'is_weekend',
        'historical_response_rate',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'responded_at' => 'datetime',
        'response_time_minutes' => 'integer',
        'distance_km' => 'decimal:2',
        'days_since_last_donation' => 'integer',
        'temporal_hour' => 'integer',
        'is_weekend' => 'boolean',
        'historical_response_rate' => 'float',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'request_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
}
