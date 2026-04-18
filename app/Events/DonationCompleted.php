<?php

namespace App\Events;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $donor,
        public readonly BloodRequest $bloodRequest,
        public readonly bool $isEmergency = false,
        public readonly bool $isFirstResponder = false,
    ) {}
}
