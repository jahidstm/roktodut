<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequestReport extends Model
{
    protected $fillable = [
        'user_id',
        'blood_request_id',
        'reason',
        'status',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }
}
