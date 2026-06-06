<?php

namespace App\Policies;

use App\Models\DonorAvailability;
use App\Models\User;

class DonorAvailabilityPolicy
{
    public function update(User $user, DonorAvailability $availability): bool
    {
        return $user->id === $availability->user_id;
    }

    public function delete(User $user, DonorAvailability $availability): bool
    {
        return $user->id === $availability->user_id;
    }
}
