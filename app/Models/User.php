<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        return $this->hasMany(BloodRequestResponse::class, 'donor_id');
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

    // ✅ Corrected Relationship for CustomNotification
    public function notifications(): HasMany
    {
        return $this->hasMany(CustomNotification::class);
    }

    // ==================== Helper Methods ====================

    public function isDonor(): bool
    {
        return $this->role === UserRole::DONOR;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOrgAdmin(): bool
    {
        return $this->role === UserRole::ORG_ADMIN;
    }

    public function isInCooldown(): bool
    {
        return $this->cooldown_until && $this->cooldown_until->isFuture();
    }
}
