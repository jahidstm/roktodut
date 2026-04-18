<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail; // আপাতত বন্ধ রাখা হয়েছে
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class User extends Authenticatable // implements MustVerifyEmail — আপাতত বন্ধ
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'blood_group',
        'division_id',
        'district_id',
        'upazila_id',
        'organization_id',
        'address',
        'gender',
        'weight',
        'date_of_birth',
        'profile_image',
        'edu_email',
        'latitude',
        'longitude',
        'is_available',
        'is_ready_now',
        'hide_phone',
        'cooldown_until',
        'total_donations',
        'points',
        'reward_points',
        'total_verified_donations',
        'referral_code',
        'referred_by',
        'monthly_points',
        'monthly_points_month',
        'is_campus_hero',
        'verified_badge',
        'nid_image',
        'nid_path',
        'nid_number',
        'nid_status',
        'qr_token',             // 🔐 Dynamic QR Smart Card token
        'last_login_at',
        'welcome_back_checked',
        'last_donated_at',      // 🎯 ডাটাবেসের আসল কলাম
        'is_onboarded',
        'email_verified_at',
        'remember_token',
        'provider',
        'provider_id',
        'is_shadowbanned',
        'opt_out_org_broadcast',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nid_image',
        'qr_token',             // 🔐 JSON/API response-এ কখনো expose করা যাবে না
        'phone',                // Privacy Shield — সরাসরি serialize-এ দেখা যাবে না
        'email',                // Privacy Shield
        'nid_number',           // Privacy Shield
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
            'blood_group'       => BloodGroup::class,
            'is_available'      => 'boolean',
            'is_ready_now'      => 'boolean',
            'hide_phone'        => 'boolean',
            'verified_badge'    => 'boolean',
            'welcome_back_checked' => 'boolean',
            'cooldown_until'    => 'datetime',
            'last_login_at'     => 'datetime',
            'date_of_birth'     => 'date',
            'last_donated_at'   => 'date',
            'is_onboarded'      => 'boolean',
            'is_campus_hero'    => 'boolean',
            'is_shadowbanned'   => 'boolean',
            'opt_out_org_broadcast' => 'boolean',
        ];
    }

    // ==================== 📍 Location Relationships ====================

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user.'.$this->id;
    }

    // ==================== Other Relationships ====================

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class, 'requested_by');
    }

    public function bloodRequestResponses(): HasMany
    {
        return $this->hasMany(BloodRequestResponse::class, 'donor_user_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_members')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'admin_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'author_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_user_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    public function campAttendances(): HasMany
    {
        return $this->hasMany(CampAttendance::class);
    }

    public function createdCamps(): HasMany
    {
        return $this->hasMany(BloodCamp::class, 'created_by');
    }

    public function broadcastLogs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'broadcasted_by');
    }

    // ==================== Helper Methods (THE FIX) ====================

    public function isDonor(): bool
    {
        return ($this->role?->value ?? null) === UserRole::DONOR->value;
    }

    public function isAdmin(): bool
    {
        return ($this->role?->value ?? null) === UserRole::ADMIN->value;
    }

    public function isOrgAdmin(): bool
    {
        return ($this->role?->value ?? null) === UserRole::ORG_ADMIN->value;
    }

    public function isInCooldown(): bool
    {
        return $this->cooldown_until && $this->cooldown_until->isFuture();
    }

    // 🎯 FIX: 'last_donated_at' কলাম ব্যবহার করা হলো
    public function canDonate(): bool
    {
        if (!$this->last_donated_at) {
            return true;
        }
        return $this->last_donated_at->copy()->addDays(120)->isPast();
    }

    // 🎯 FIX: 'last_donated_at' কলাম ব্যবহার করা হলো
    public function daysUntilNextDonation(): int
    {
        if (!$this->last_donated_at) {
            return 0;
        }
        $nextDonationDate = $this->last_donated_at->copy()->addDays(120);
        return max(0, (int) now()->diffInDays($nextDonationDate, false));
    }

    // 🎯 FIX: 'last_donated_at' কলাম ব্যবহার করা হলো
    public function getNextEligibleDateAttribute()
    {
        return $this->last_donated_at ? $this->last_donated_at->copy()->addDays(120) : null;
    }

    public function getIsEligibleToDonateAttribute()
    {
        return $this->canDonate();
    }

    public function scopeInSameOrganization($query)
    {
        if (Auth::check() && Auth::user()->role === 'org_admin') {
            return $query->where('organization_id', Auth::user()->organization_id);
        }
        return $query;
    }

    /**
     * শ্যাডোব্যান্ড ইউজারদের লিডারবোর্ড থেকে ফিল্টার করার স্কোপ।
     * লিডারবোর্ড কোয়েরি যেখানে ব্যবহার করুন: ->notShadowbanned()->বাকি কোয়েরি
     */
    public function scopeNotShadowbanned($query)
    {
        return $query->where('is_shadowbanned', false);
    }

    /**
     * Get Profile Completion Percentage
     * 
     * @return int
     */
    public function getProfileCompletionAttribute(): int
    {
        $completion = 0;

        if (!empty($this->name)) $completion += 15;
        if (!empty($this->phone)) $completion += 15;
        if (!empty($this->blood_group)) $completion += 20;
        if (!empty($this->division_id)) $completion += 10;
        if (!empty($this->district_id)) $completion += 10;
        if (!empty($this->upazila_id)) $completion += 10;
        if ($this->nid_status === 'verified') $completion += 20;

        return $completion;
    }
}
