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
        'needed_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'blood_group' => BloodGroup::class,
            'urgency'     => UrgencyLevel::class,
            'needed_at'   => 'datetime', 
        ];
    }

    /**
     * রিকোয়েস্টের মালিক (Requester)
     */
    public function requester(): BelongsTo
    {
        // ফরেন কি 'requested_by' স্পষ্টভাবে ডিফাইন করা আছে, যা একদম সঠিক।
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * এই রিকোয়েস্টের বিপরীতে আসা ডোনারদের রেসপন্স
     */
    public function responses(): HasMany
    {
        // 🚨 আপডেট: 'blood_request_id' স্পষ্টভাবে বলে দেওয়া হলো যাতে কোনো ম্যাজিকের ওপর নির্ভর করতে না হয়।
        return $this->hasMany(BloodRequestResponse::class, 'request_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // --- Status Helpers ---

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