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

        // 📍 নতুন রিলেশনাল লোকেশন আইডি (Mass Assignment Security Unlocked)
        'division_id',
        'district_id',
        'upazila_id',

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
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * এই রিকোয়েস্টের বিপরীতে আসা ডোনারদের রেসপন্স
     */
    public function responses(): HasMany
    {
        return $this->hasMany(BloodRequestResponse::class, 'blood_request_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // 📍 নতুন যুক্ত করা লোকেশন রিলেশনশিপস (Eager Loading এর জন্য)

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    // ── ৭. Query Scopes ────────────────────────────────────────

    /**
     * শুধুমাত্র সক্রিয় (pending + ভবিষ্যতের) রিকোয়েস্ট।
     * needed_at NULL বা ভবিষ্যতের হলে সক্রিয় ধরা হবে।
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('needed_at')
                  ->orWhere('needed_at', '>=', now()->subHours(2));
            });
    }

    /**
     * Local Emergency Radar: জেলা ও ব্লাড গ্রুপ অনুযায়ী ফিল্টার।
     * compatible_groups: নিজের গ্রুপকেও অন্তর্ভুক্ত করে।
     */
    public function scopeLocalRadar($query, int $districtId, array $bloodGroups)
    {
        return $query->where('district_id', $districtId)
            ->whereIn('blood_group', $bloodGroups);
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
