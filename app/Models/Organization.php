<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    // 🚀 THE FIX: ডাটাবেসের সব কলামের নাম এখানে যুক্ত করা হলো
    protected $fillable = [
        'name',
        'short_name',
        'type',
        'established_year',
        'division',
        'district',
        'upazila',
        'address',
        'phone',
        'email',
        'logo',
        'document_path',
        'description',
        'admin_id',
        'status',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function approvedMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'approved');
    }

    public function pendingMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    public function locationDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district');
    }

    public function locationUpazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila');
    }

    public function bloodCamps(): HasMany
    {
        return $this->hasMany(BloodCamp::class);
    }

    public function broadcastLogs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class);
    }
}
