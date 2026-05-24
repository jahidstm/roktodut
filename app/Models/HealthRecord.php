<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'systolic_bp',
        'diastolic_bp',
        'hemoglobin_level',
        'weight_kg',
        'recorded_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'systolic_bp' => 'integer',
            'diastolic_bp' => 'integer',
            'hemoglobin_level' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
