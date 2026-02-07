<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'patient_name',
        'blood_group',
        'bags_needed',
        'hospital',
        'district',
        'thana',
        'address',
        'contact_name',
        'contact_number',
        'urgency',
        'needed_by',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'blood_group' => BloodGroup::class,
            'urgency'     => UrgencyLevel::class,
            'needed_by'   => 'date',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(BloodRequestResponse::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFulfilled(): bool
    {
        return $this->status === 'fulfilled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
}
