<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DonationRecordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'last_donated_at' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $request->user()->update([
            'last_donated_at' => $request->last_donated_at,
        ]);

        return back()->with('success', 'আপনার সর্বশেষ রক্তদানের তথ্য সফলভাবে আপডেট হয়েছে।');
    }
}