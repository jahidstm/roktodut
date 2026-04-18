<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampRegistration extends Model
{
    protected $fillable = [
        'camp_id',
        'user_id',
        'status',
    ];

    public function camp(): BelongsTo
    {
        return $this->belongsTo(BloodCamp::class, 'camp_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
