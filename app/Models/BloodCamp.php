<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodCamp extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'camp_date',
        'location',
        'start_at',
        'end_at',
        'district_id',
        'upazila_id',
        'address_line',
        'contact_name',
        'contact_phone',
        'notes',
        'target_donors',
        'is_public',
        'status',
        'created_by',
    ];

    protected $casts = [
        'camp_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(CampAttendance::class, 'blood_camp_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CampRegistration::class, 'camp_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        if ($this->status === 'published' && $this->end_at && $this->end_at->isPast()) {
            return 'completed';
        }

        return $this->status ?: 'draft';
    }
}
