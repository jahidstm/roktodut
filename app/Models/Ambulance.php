<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'type',
        'division_id',
        'district_id',
        'upazila_id',
        'organization_id',
        'added_by',
        'is_verified',
        'status',
        'vehicle_number',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function adder()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
