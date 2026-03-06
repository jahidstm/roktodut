<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show()
    {
        return view('auth.onboarding');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => ['required', 'string', 'in:donor,recipient'],
            'phone' => ['required_if:role,donor', 'nullable', 'string', 'max:20'],
            'blood_group' => ['required_if:role,donor', 'nullable', 'string', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
        ]);

        $user = auth()->user();
        
        $user->update([
            'role' => $request->role,
            'phone' => $request->role === 'donor' ? $request->phone : null,
            'blood_group' => $request->role === 'donor' ? $request->blood_group : null,
            'is_onboarded' => true,
        ]);

        return redirect()->route('dashboard');
    }
}