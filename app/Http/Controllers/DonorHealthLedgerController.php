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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hemoglobin_level' => ['nullable', 'numeric', 'min:5', 'max:25'],
            'systolic_bp'      => ['nullable', 'integer', 'min:70', 'max:200'],
            'diastolic_bp'     => ['nullable', 'integer', 'min:40', 'max:130'],
            'weight_kg'        => ['nullable', 'numeric', 'min:30', 'max:200'],
            'recorded_at'      => ['required', 'date', 'before_or_equal:today'],
        ]);

        if (empty($validated['hemoglobin_level']) && empty($validated['systolic_bp']) && empty($validated['weight_kg'])) {
            return back()->with('error', 'অন্তত একটি হেলথ মেট্রিক (হিমোগ্লোবিন, প্রেশার বা ওজন) ইনপুট দিন।');
        }

        HealthRecord::create([
            'user_id'          => $request->user()->id,
            'recorded_at'      => $validated['recorded_at'],
            'hemoglobin_level' => $validated['hemoglobin_level'],
            'systolic_bp'      => $validated['systolic_bp'],
            'diastolic_bp'     => $validated['diastolic_bp'],
            'weight_kg'        => $validated['weight_kg'],
            'source'           => 'self_reported',
        ]);

        return back()->with('success', 'আপনার হেলথ রেকর্ড সফলভাবে যুক্ত হয়েছে।');
    }
}
