<?php

namespace App\Models;

use App\Enums\DonationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'blood_request_id',
        'donation_date',
        'hospital',
        'district',
        'claim_status',
        'claim_deadline',
        'points_earned',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'claim_status'  => DonationStatus::class,
            'donation_date'  => 'date',
            'claim_deadline' => 'datetime',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function isPending(): bool
    {
        return $this->claim_status === DonationStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->claim_status === DonationStatus::CONFIRMED;
    }

    public function isAutoApproved(): bool
    {
        return $this->claim_status === DonationStatus::AUTO_APPROVED;
    }
}
