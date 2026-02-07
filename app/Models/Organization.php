<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'district',
        'address',
        'phone',
        'email',
        'logo',
        'description',
        'admin_id',
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
}
