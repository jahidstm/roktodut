<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampAttendance extends Model
{
    protected $fillable = [
        'blood_camp_id',
        'user_id',
        'points_awarded',
    ];

    public function bloodCamp()
    {
        return $this->belongsTo(BloodCamp::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
