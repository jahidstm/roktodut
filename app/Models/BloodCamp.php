<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodCamp extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'camp_date',
        'location',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'camp_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(CampAttendance::class, 'blood_camp_id');
    }
}
