<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    protected $fillable = [
        'organization_id',
        'blood_request_id',
        'broadcasted_by',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function broadcaster()
    {
        return $this->belongsTo(User::class, 'broadcasted_by');
    }
}
