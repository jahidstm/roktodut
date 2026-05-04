<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'name_bn',
        'aliases',
        'district_id',
        'upazila_id',
        'is_verified',
    ];

    protected $casts = [
        'aliases'     => 'array',
        'is_verified' => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    // ─────────────────────────────────────────────────────────────
    // Search: name, name_bn এবং aliases-এ খোঁজা
    // ─────────────────────────────────────────────────────────────
    public function scopeSearch($query, string $term)
    {
        $like = '%' . $term . '%';
        return $query->where(function ($q) use ($like, $term) {
            $q->where('name', 'LIKE', $like)
              ->orWhere('name_bn', 'LIKE', $like)
              ->orWhereJsonContains('aliases', $term);
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Display name helper (বাংলা থাকলে বাংলা, না হলে ইংরেজি)
    // ─────────────────────────────────────────────────────────────
    public function getDisplayNameAttribute(): string
    {
        return $this->name_bn ?? $this->name;
    }
}
