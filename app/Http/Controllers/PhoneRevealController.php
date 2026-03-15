<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\PhoneRevealLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PhoneRevealController extends Controller
{
    public function reveal(Request $request, BloodRequest $bloodRequest, User $donor)
    {
        // Only allowed users can reveal accepted donor phone
        Gate::authorize('viewAcceptedDonors', $bloodRequest);

        // donor must have accepted this request
        $accepted = $bloodRequest->responses()
            ->where('user_id', $donor->id)
            ->where('status', 'accepted')
            ->exists();

        abort_unless($accepted, 404);

        PhoneRevealLog::firstOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'viewer_user_id' => $request->user()->id,
                'donor_user_id' => $donor->id,
            ],
            [
                'revealed_at' => now(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        // Return phone (simple MVP). Later you can mask/OTP etc.
        return response()->json([
            'donor_user_id' => $donor->id,
            'phone' => $donor->phone,
        ]);
    }
}