<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'guest_token',
        'created_ip_hash',
        'patient_name',
        'blood_group',
        'bags_needed',
        'hospital_id',

        // 📍 নতুন রিলেশনাল লোকেশন আইডি (Mass Assignment Security Unlocked)
        'division_id',
        'district_id',
        'upazila_id',

        'address',
        'latitude',
        'longitude',
        'contact_name',
        'contact_number',
        'contact_number_normalized',
        'urgency',
        'needed_at',
        'status',
        'notes',
        'is_phone_hidden',
        'spam_report_count',
        'managed_report_count',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'blood_group'     => BloodGroup::class,
            'urgency'         => UrgencyLevel::class,
            'needed_at'       => 'datetime',
            'is_phone_hidden' => 'boolean',
            'is_hidden'       => 'boolean',
        ];
    }

    /**
     * Reports on this request
     */
    public function reports()
    {
        return $this->hasMany(BloodRequestReport::class, 'blood_request_id');
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

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function broadcastLogs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'blood_request_id');
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

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
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
     * 📍 Radius-based scope (Haversine Formula)
     *
     * নির্দিষ্ট lat/lng কেন্দ্র থেকে $radiusKm কিলোমিটারের মধ্যে
     * থাকা সকল blood_requests ফেরত দেয়, দূরত্ব অনুযায়ী sort করে।
     *
     * Usage:  BloodRequest::closeTo($lat, $lng, 5)->active()->get();
     */
    public function scopeCloseTo($query, float $lat, float $lng, float $radiusKm = 5.0)
    {
        $haversine = "(6371 * acos(
            cos(radians({$lat}))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians({$lng}))
            + sin(radians({$lat})) * sin(radians(latitude))
        ))";

        return $query
            ->selectRaw("*, {$haversine} AS distance_km")
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->havingRaw("{$haversine} <= ?", [$radiusKm])
            ->orderByRaw("{$haversine} ASC");
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
