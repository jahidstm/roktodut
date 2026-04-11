<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityRadarEvent extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'event_type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
