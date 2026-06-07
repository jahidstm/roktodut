<?php

namespace App\Models;

use App\Enums\BloodComponentType;
use App\Enums\BloodGroup;
use App\Enums\UrgencyLevel;
use Carbon\Carbon;
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
        'condition_type',
        'notes_for_donor',
        'is_phone_hidden',
        'cadence_days',
        'lead_time_days',
        'next_needed_at',
        'last_dispatched_for',
        'is_active',
        'is_paused',
        'paused_until',
        'status_reason',
        'buddy_rotation_index',
    ];

    protected function casts(): array
    {
        return [
            'blood_group'         => BloodGroup::class,
            'component_type'      => BloodComponentType::class,
            'urgency'             => UrgencyLevel::class,
            'is_phone_hidden'     => 'boolean',
            'next_needed_at'      => 'datetime',
            'last_dispatched_for' => 'date',
            'is_active'           => 'boolean',
            'is_paused'           => 'boolean',
            'paused_until'        => 'datetime',
        ];
    }

    // ── Condition Type Labels ────────────────────────────────────
    public const CONDITION_TYPES = [
        'thalassemia'  => 'থ্যালাসেমিয়া',
        'dialysis'     => 'ডায়ালাইসিস',
        'sickle_cell'  => 'সিকেল সেল',
        'cancer'       => 'ক্যান্সার',
        'other'        => 'অন্যান্য',
    ];

    public const CONDITION_COLORS = [
        'thalassemia'  => 'purple',
        'dialysis'     => 'blue',
        'sickle_cell'  => 'amber',
        'cancer'       => 'red',
        'other'        => 'slate',
    ];

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_paused', false);
    }

    public function scopePaused($query)
    {
        return $query->where('is_paused', true);
    }

    public function scopeDue($query)
    {
        return $query
            ->where('is_active', true)
            ->where('is_paused', false)
            ->where('next_needed_at', '<=', now()->addDays(30));
    }

    // ── Computed Attributes ──────────────────────────────────────

    /** কত দিন পরে পরবর্তী রক্তের প্রয়োজন */
    public function getDaysUntilNextAttribute(): ?int
    {
        if (! $this->next_needed_at) {
            return null;
        }
        return max(0, (int) now()->startOfDay()->diffInDays($this->next_needed_at->startOfDay(), false));
    }

    /** Dispatch হবার তারিখ (next_needed_at - lead_time_days) */
    public function getNextDispatchDateAttribute(): ?Carbon
    {
        if (! $this->next_needed_at) {
            return null;
        }
        return $this->next_needed_at->copy()->subDays((int) $this->lead_time_days);
    }

    /** Condition type label in Bengali */
    public function getConditionLabelAttribute(): string
    {
        return self::CONDITION_TYPES[$this->condition_type] ?? 'অজানা';
    }

    /** Condition color for badge */
    public function getConditionColorAttribute(): string
    {
        return self::CONDITION_COLORS[$this->condition_type] ?? 'slate';
    }

    /** পরবর্তী ৩টি dispatch তারিখ */
    public function getUpcomingDatesAttribute(): array
    {
        if (! $this->next_needed_at || ! $this->cadence_days) {
            return [];
        }
        $dates = [];
        $base = $this->next_needed_at->copy();
        for ($i = 0; $i < 3; $i++) {
            $dates[] = $base->copy()->addDays($i * (int) $this->cadence_days);
        }
        return $dates;
    }

    /** সাবস্ক্রিপশন সচল কিনা (is_active + not paused) */
    public function getIsRunningAttribute(): bool
    {
        return $this->is_active && ! $this->is_paused;
    }

    // ── Relationships ────────────────────────────────────────────
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

    public function buddies(): HasMany
    {
        return $this->hasMany(ChronicSubscriptionBuddy::class, 'subscription_id');
    }

    /** Auto-generated BloodRequests linked to this subscription */
    public function dispatchedRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class, 'requested_by', 'user_id')
            ->where('notes', 'like', '%[Auto-generated from chronic plan]%')
            ->latest('needed_at');
    }
}
