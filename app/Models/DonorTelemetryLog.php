<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorTelemetryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_request_id',
        'notification_type',
        'latency_ms',
        'ignored',
        'distance_km',
    ];

    protected $casts = [
        'ignored' => 'boolean',
        'distance_km' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
