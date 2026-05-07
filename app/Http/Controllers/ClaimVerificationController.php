<?php

namespace App\Http\Controllers;

use App\Models\OfflineDonationClaim;
use App\Services\OfflineClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ClaimVerificationController extends Controller
{
    public function __construct(
        private readonly OfflineClaimService $offlineClaimService,
    ) {}

    public function show(OfflineDonationClaim $claim)
    {
        $claim->loadMissing('donor');

        $confirmUrl = URL::temporarySignedRoute(
            'offline.confirm',
            $claim->expires_at ?? now()->addHours(48),
            ['claim' => $claim->id]
        );

        return view('offline-verify', [
            'claim' => $claim,
            'confirmUrl' => $confirmUrl,
            'result' => null,
        ]);
    }

    public function confirm(Request $request, OfflineDonationClaim $claim)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:yes,no'],
        ]);

        $approved = $validated['decision'] === 'yes';
        $claim = $this->offlineClaimService->confirmByRecipient($claim, $approved);

        return view('offline-verify', [
            'claim' => $claim->loadMissing('donor'),
            'confirmUrl' => null,
            'result' => $approved ? 'approved' : 'rejected',
        ]);
    }

    public function adminApprove(Request $request, OfflineDonationClaim $claim)
    {
        $this->offlineClaimService->approveByAdmin($claim, $request->user());

        return back()->with('success', 'অফলাইন ডোনেশন ক্লেইম অনুমোদন করা হয়েছে।');
    }
}
