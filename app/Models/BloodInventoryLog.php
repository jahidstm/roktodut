<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventoryLog extends Model
{
    public $timestamps = false; // immutable — শুধু created_at আছে

    protected $fillable = [
        'organization_id',
        'blood_group',
        'units',
        'action',
        'recorded_by',
    ];

    protected $casts = [
        'units'      => 'integer',
        'created_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes — Time-Series Queries
    // ─────────────────────────────────────────────────────────────
    public function scopeForOrg($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    public function scopeForBloodGroup($query, string $group)
    {
        return $query->where('blood_group', $group);
    }

    public function scopeInDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeLastNDays($query, int $days)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
