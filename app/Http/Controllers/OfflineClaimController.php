<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\OfflineClaimData;
use App\Http\Requests\StoreOfflineClaimRequest;
use App\Services\OfflineClaimService;

class OfflineClaimController extends Controller
{
    public function __construct(
        private readonly OfflineClaimService $offlineClaimService,
    ) {}

    public function store(StoreOfflineClaimRequest $request)
    {
        $proofPath = $request->file('proof_path')?->store('offline_donation_proofs', 'private');
        $claimData = OfflineClaimData::fromValidated($request->validated(), $proofPath);

        $this->offlineClaimService->processClaim(
            donor: $request->user(),
            data: $claimData,
            ipAddress: (string) $request->ip(),
        );

        return redirect()->route('dashboard')->with('success', 'অফলাইন রক্তদান ক্লেইম সফলভাবে জমা হয়েছে।');
    }
}
