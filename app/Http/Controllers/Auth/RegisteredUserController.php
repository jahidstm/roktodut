<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Architecture:
     * - "is_donor" boolean controls search visibility (not role enum).
     * - donor: phone + blood_group required. is_donor = true.
     * - recipient: phone + blood_group nullable (optional for analytics). is_donor = false.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isDonor = $request->input('registration_intent') === 'donor';

        $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'           => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_intent'=> ['required', 'string', 'in:donor,recipient'],

            // ✅ The Final 1% Polish: Dynamic validation via required_if
            'phone'              => [
                'required_if:registration_intent,donor',
                'nullable', 'string', 'max:20', 'unique:users,phone'
            ],
            'blood_group'        => [
                'required_if:registration_intent,donor',
                'nullable', 'string', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'
            ],

            'referred_by_code'   => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        $referredById = null;
        if ($request->filled('referred_by_code')) {
            $referrer = User::where('referral_code', $request->referred_by_code)->first();
            if ($referrer) {
                $referredById = $referrer->id;
            }
        }

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $isDonor ? 'donor' : 'recipient', // keep role for backward compat
            'is_donor'     => $isDonor,                          // ✅ new flexible flag
            'phone'        => $isDonor ? $request->phone : null,
            // ✅ Blood group: save even for recipients (optional analytics goldmine)
            'blood_group'  => $request->filled('blood_group') ? $request->blood_group : null,
            'referred_by'  => $referredById,

            // Recipient → skip onboarding. Donor → must complete onboarding.
            'is_onboarded' => !$isDonor,
        ]);

        if ($referredById) {
            $referrerModel = User::find($referredById);
            if ($referrerModel) {
                app(\App\Services\GamificationService::class)->awardReferralSignupPoints($referrerModel);
            }
        }

        event(new Registered($user));
        Auth::login($user);

        // Recipient → home. Donor → onboarding.
        return $isDonor
            ? redirect()->route('onboarding.show')
            : redirect()->route('home');
    }
}
