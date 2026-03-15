<?php

namespace App\Policies;

use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function view(User $user, BloodRequest $bloodRequest): bool
    {
        return true;
    }

    public function respond(User $user, BloodRequest $bloodRequest): bool
    {
        if ((int) $bloodRequest->requested_by === (int) $user->id) {
            return false;
        }

        if (in_array($bloodRequest->status, ['fulfilled', 'expired'], true)) {
            return false;
        }

        return true;
    }

    public function markFulfilled(User $user, BloodRequest $bloodRequest): bool
    {
        if ((int) $bloodRequest->requested_by !== (int) $user->id) {
            return false;
        }

        if (in_array($bloodRequest->status, ['fulfilled', 'expired'], true)) {
            return false;
        }

        return true;
    }

    public function viewAcceptedDonors(User $user, BloodRequest $bloodRequest): bool
    {
        if ((int) $bloodRequest->requested_by === (int) $user->id) {
            return true;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('org_admin'))) {
            return true;
        }

        return false;
    }
}