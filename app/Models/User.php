<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'blood_group',
        'district',
        'upazila',
        'address',
        'gender',
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
        'verified_badge',
        'nid_image',
        'nid_status',
        'last_login_at',
        'welcome_back_checked',
        'last_donated_at', // এটিই এখন আমাদের মেইন কলাম

        'is_onboarded',
        'email_verified_at',
        'remember_token',

        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nid_image',
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
            'last_donated_at'   => 'date', // নিশ্চিত করা হলো এটি ডেট হিসেবে কাস্ট হচ্ছে
            'is_onboarded'      => 'boolean',
        ];
    }

    // ==================== Relationships ====================

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

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    // ==================== Helper Methods ====================

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

    /**
     * ✅ ফিক্সড: এখন এটি last_donated_at চেক করবে (৯০ দিনের রুল)
     */
    public function canDonate(): bool
    {
        if (!$this->last_donated_at) {
            return true;
        }
        
        return $this->last_donated_at->copy()->addDays(90)->isPast();
    }

    /**
     * ✅ ফিক্সড: পরবর্তী রক্তদানের কাউন্টডাউন
     */
    public function daysUntilNextDonation(): int
    {
        if (!$this->last_donated_at) {
            return 0;
        }
        
        $nextDonationDate = $this->last_donated_at->copy()->addDays(90);
        return max(0, (int) now()->diffInDays($nextDonationDate, false));
    }

    /**
     * ড্যাশবোর্ড এবং ফ্রন্টএন্ডের জন্য সহজ অ্যাক্সেসর
     */
    public function getNextEligibleDateAttribute()
    {
        return $this->last_donated_at ? $this->last_donated_at->copy()->addDays(90) : null;
    }

    public function getIsEligibleToDonateAttribute()
    {
        return $this->canDonate();
    }
}