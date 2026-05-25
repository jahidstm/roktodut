<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Services\HealthVelocityService;
use Illuminate\Http\Request;

class DonorHealthLedgerController extends Controller
{
    public function index(Request $request, HealthVelocityService $service)
    {
        $user = $request->user();
        $records = HealthRecord::query()
            ->where('user_id', $user->id)
            ->orderBy('recorded_at')
            ->get();

        $analysis = $service->analyze($records);

        return view('health-ledger.index', [
            'records' => $records,
            'nudges' => $analysis['nudges'],
            'charts' => $analysis['charts'],
        ]);
    }
}
