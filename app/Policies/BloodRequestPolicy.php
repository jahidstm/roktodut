<?php

namespace App\Policies;

use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function respond(User $user, BloodRequest $bloodRequest): bool
    {
        // নিজের রিকোয়েস্টে নিজে respond করার দরকার নেই
        if ((int) $bloodRequest->requested_by === (int) $user->id) {
            return false;
        }

        // fulfilled/expired হলে respond করা যাবে না
        if (in_array($bloodRequest->status, ['fulfilled', 'expired'], true)) {
            return false;
        }

        return true;
    }

    public function markFulfilled(User $user, BloodRequest $bloodRequest): bool
    {
        // শুধু owner পারবে
        if ((int) $bloodRequest->requested_by !== (int) $user->id) {
            return false;
        }

        // already fulfilled/expired হলে না
        if (in_array($bloodRequest->status, ['fulfilled', 'expired'], true)) {
            return false;
        }

        return true;
    }
}