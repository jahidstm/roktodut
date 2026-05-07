<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineDonationClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'recipient_phone',
        'recipient_phone_normalized',
        'patient_name',
        'district_id',
        'hospital_name',
        'donation_date',
        'proof_path',
        'status',
        'verification_method',
        'matched_request_id',
        'verified_by_id',
        'risk_score',
        'expires_at',
        'ip_hash',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'donation_date' => 'date',
            'expires_at' => 'datetime',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function matchedRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'matched_request_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }
}
