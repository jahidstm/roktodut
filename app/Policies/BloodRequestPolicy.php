<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function markFulfilled(User $user, BloodRequest $request): bool
    {
        return (int) $request->requested_by === (int) $user->id
            && in_array($request->status, ['pending', 'in_progress'], true);
    }

    public function respond(User $user, BloodRequest $request): bool
    {
        if (($user->role ?? null) !== UserRole::DONOR->value) return false;
        if ((int) $request->requested_by === (int) $user->id) return false;
        if ($request->status !== 'pending') return false;
        return true;
    }
}