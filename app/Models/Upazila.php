<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upazila extends Model
{
    protected $fillable = ['district_id', 'name', 'type'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function isArea(): bool
    {
        return $this->type === 'area';
    }

    public function isUpazila(): bool
    {
        return $this->type === 'upazila';
    }
}
