<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloodRequestResponseController extends Controller
{
    public function store(Request $request, BloodRequest $bloodRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['accepted', 'declined'])],
        ]);

        BloodRequestResponse::updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => $data['status'],
            ]
        );

        return back()->with('success', 'আপনার রেসপন্স সংরক্ষণ হয়েছে।');
    }
}