<?php

namespace App\Models;

use App\Enums\BloodComponentType;
use App\Enums\BloodGroup;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChronicRequestSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_blood_request_id',
        'patient_name',
        'blood_group',
        'component_type',
        'bags_needed',
        'hospital_id',
        'division_id',
        'district_id',
        'upazila_id',
        'address',
        'contact_name',
        'contact_number',
        'contact_number_normalized',
        'urgency',
        'notes',
        'is_phone_hidden',
        'cadence_days',
        'lead_time_days',
        'next_needed_at',
        'last_dispatched_for',
        'is_active',
        'buddy_rotation_index',
    ];

    protected function casts(): array
    {
        return [
            'blood_group' => BloodGroup::class,
            'component_type' => BloodComponentType::class,
            'urgency' => UrgencyLevel::class,
            'is_phone_hidden' => 'boolean',
            'next_needed_at' => 'datetime',
            'last_dispatched_for' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'source_blood_request_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function buddies(): HasMany
    {
        return $this->hasMany(ChronicSubscriptionBuddy::class, 'subscription_id');
    }
}
