<?php

namespace App\Models;

use App\Enums\BloodGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodInventory extends Model
{
    protected $fillable = [
        'organization_id',
        'blood_group',
        'units_available',
        'is_accepting_donations',
        'notes',
    ];

    protected $casts = [
        'is_accepting_donations' => 'boolean',
        'units_available'        => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BloodInventoryLog::class, 'organization_id', 'organization_id')
                    ->where('blood_group', $this->blood_group);
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('units_available', '>', 0);
    }

    public function scopeByBloodGroup($query, string $group)
    {
        return $query->where('blood_group', $group);
    }

    public function scopeAccepting($query)
    {
        return $query->where('is_accepting_donations', true);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Stock level: পর্যাপ্ত / সীমিত / নেই
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->units_available >= 5)  return 'adequate';
        if ($this->units_available >= 1)  return 'limited';
        return 'empty';
    }

    /**
     * বাংলা label
     */
    public function getStockLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'adequate' => 'পর্যাপ্ত',
            'limited'  => 'সীমিত',
            default    => 'নেই',
        };
    }

    /**
     * Tailwind badge CSS classes
     */
    public function getStockBadgeClassAttribute(): string
    {
        return match ($this->stock_status) {
            'adequate' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'limited'  => 'bg-amber-100 text-amber-700 border-amber-200',
            default    => 'bg-red-100 text-red-600 border-red-200',
        };
    }

    /**
     * Emoji indicator
     */
    public function getStockEmojiAttribute(): string
    {
        return match ($this->stock_status) {
            'adequate' => '✅',
            'limited'  => '⚠️',
            default    => '❌',
        };
    }

    /**
     * এই inventory-র snapshot log করা (Ledger Pattern)
     */
    public function logSnapshot(int $recordedBy = null, string $action = 'manual_update'): BloodInventoryLog
    {
        return BloodInventoryLog::create([
            'organization_id' => $this->organization_id,
            'blood_group'     => $this->blood_group,
            'units'           => $this->units_available,
            'action'          => $action,
            'recorded_by'     => $recordedBy,
        ]);
    }
}
