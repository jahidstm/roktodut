<?php

namespace App\Http\Controllers;

use App\Enums\UrgencyLevel;
use App\Events\DonationCompleted;
use App\Http\Requests\StoreBloodRequestRequest;
use App\Jobs\SendEmergencyBloodRequestNotificationJob;
use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\District;
use App\Services\DonorMatchingService;
use App\Services\MathCaptchaService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BloodRequestController extends Controller
{
    /**
     * সকল পেন্ডিং রিকোয়েস্ট দেখায় (Advanced Filtering ও Eager Loading সহ)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = BloodRequest::query()
            ->with([
                'requester:id,name',
                'responses' => fn($q) => $q->where('user_id', $userId),
                'district:id,name', // 🚀 ইগার লোডিং (পারফরম্যান্স অপ্টিমাইজেশন)
                'upazila:id,name'
            ])
            ->withCount([
                'responses as accepted_responses_count' => fn($q) => $q->where('status', 'accepted'),
                'responses as claimed_verifications_count' => fn($q) => $q->where('verification_status', 'claimed'),
                'responses as verified_verifications_count' => fn($q) => $q->where('verification_status', 'verified'),
            ])
            ->where('status', 'pending');

        // 🎯 স্মার্ট ফিল্টারিং লজিক (Relational IDs)
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('upazila_id')) {
            $query->where('upazila_id', $request->upazila_id);
        }

        $requests = $query->orderByRaw('needed_at is null asc')
            ->orderBy('needed_at')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('requests.index', compact('requests'));
    }

    /**
     * রিকোয়েস্ট তৈরির ফর্ম দেখায়
     */
    public function create(Request $request, MathCaptchaService $mathCaptchaService)
    {
        $captchaQuestion = $mathCaptchaService->generate();

        if ($request->user()) {
            return view('requests.create', compact('captchaQuestion'));
        }

        $token = $request->cookie('rd_guest_token');
        $isValidLength = is_string($token) && strlen($token) >= 32 && strlen($token) <= 64;

        if (!$isValidLength) {
            $token = Str::random(64);
        }

        return response()
            ->view('requests.create', compact('captchaQuestion'))
            ->cookie(
                'rd_guest_token',
                $token,
                60 * 24 * 30,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax'
            );
    }

    /**
     * নতুন রিকোয়েস্ট সেভ করা এবং ডোনারদের নোটিফাই করা
     */
    public function store(StoreBloodRequestRequest $request, MathCaptchaService $mathCaptchaService)
    {
        $ipHash = hash('sha256', ((string) $request->ip()) . '|' . ((string) config('app.key')));
        $dailyLimitMessage = 'অনেক বেশি অনুরোধ করা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।';
        $dailyKey = 'requests-store:daily:' . $ipHash;
        $dailyCount = (int) Cache::get($dailyKey, 0);

        if ($dailyCount >= 30) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $dailyLimitMessage], 429);
            }

            return back()->withInput()->with('error', $dailyLimitMessage);
        }

        if ($dailyCount === 0) {
            Cache::put($dailyKey, 1, now()->endOfDay());
        } else {
            Cache::increment($dailyKey);
        }

        $normalizedPhone = '';

        $request->validate([
            'contact_number' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use (&$normalizedPhone): void {
                    $normalizedPhone = PhoneNormalizer::normalizeBdPhone((string) $value);

                    if (preg_match('/^01\d{9}$/', $normalizedPhone) !== 1) {
                        $fail('সঠিক মোবাইল নম্বর দিন (যেমন: 01XXXXXXXXX)।');
                    }
                },
            ],
            'captcha_answer' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) use ($mathCaptchaService): void {
                    if (!$mathCaptchaService->verify($value)) {
                        $fail('ক্যাপচা সঠিক নয় বা মেয়াদ শেষ হয়েছে। আবার চেষ্টা করুন।');
                    }
                },
            ],
            'urgency' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $neededAtInput = (string) $request->input('needed_at');

                    if ($neededAtInput === '') {
                        return;
                    }

                    try {
                        $neededAt = Carbon::parse($neededAtInput);
                    } catch (\Throwable $e) {
                        return;
                    }

                    if ($value === UrgencyLevel::EMERGENCY->value && $neededAt->gt(now()->addHours(24))) {
                        $fail('জরুরি (Emergency) রিকোয়েস্টের জন্য রক্তের প্রয়োজন অবশ্যই পরবর্তী ২৪ ঘণ্টার মধ্যে হতে হবে।');
                    }

                    if ($value === UrgencyLevel::URGENT->value && $neededAt->gt(now()->addHours(72))) {
                        $fail('আর্জেন্ট (Urgent) রিকোয়েস্টের জন্য সময় অবশ্যই পরবর্তী ৭২ ঘণ্টার মধ্যে হতে হবে।');
                    }
                },
            ],
        ], [
            'contact_number.required' => 'মোবাইল নম্বর দেওয়া বাধ্যতামূলক।',
            'captcha_answer.required' => 'ক্যাপচা উত্তর দেওয়া বাধ্যতামূলক।',
        ]);

        $data = $request->validated();

        $data['contact_number_normalized'] = $normalizedPhone;
        $data['created_ip_hash'] = $ipHash;

        if ($request->user()) {
            $data['requested_by'] = $request->user()->id;
            $data['guest_token'] = null;
        } else {
            $guestToken = $request->cookie('rd_guest_token');
            $data['requested_by'] = null;
            $data['guest_token'] = is_string($guestToken) && strlen($guestToken) >= 32 && strlen($guestToken) <= 64
                ? $guestToken
                : null;
        }

        $duplicateMessage = 'আপনার এই নম্বর দিয়ে একই রক্তের গ্রুপ ও জেলার জন্য একটি অনুরোধ ইতিমধ্যে আছে। অনুগ্রহ করে আগের অনুরোধটি দেখুন বা আপডেট করুন।';
        $tooManyMessage = 'অনেক বেশি অনুরোধ করা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।';

        $existingRequest = BloodRequest::query()
            ->where('contact_number_normalized', $data['contact_number_normalized'])
            ->where('blood_group', $data['blood_group'])
            ->where('district_id', $data['district_id'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('created_at', '>=', now()->subHours(6))
            ->latest('created_at')
            ->first();

        if ($existingRequest) {
            return back()
                ->withInput()
                ->with('error', $duplicateMessage)
                ->with('existing_request_url', route('requests.show', $existingRequest));
        }

        $latestByPhone = BloodRequest::query()
            ->where('contact_number_normalized', $data['contact_number_normalized'])
            ->latest('created_at')
            ->first();

        if ($latestByPhone && $latestByPhone->created_at?->gte(now()->subMinutes(2))) {
            return back()->withInput()->with('error', $tooManyMessage);
        }

        $requestsLast24Hours = BloodRequest::query()
            ->where('contact_number_normalized', $data['contact_number_normalized'])
            ->whereNotIn('status', ['expired', 'fulfilled'])
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($requestsLast24Hours >= 3) {
            return back()->withInput()->with('error', $tooManyMessage);
        }

        $data['status'] = 'pending';

        // ১. রিকোয়েস্ট সেভ করা
        $bloodRequest = BloodRequest::create($data);

        // ২. জেলার নাম বের করা (নোটিফিকেশনে দেখানোর জন্য)
        $districtName = District::find($bloodRequest->district_id)->name ?? 'আপনার';

        // ⚙️ ৩. স্মার্ট ডোনার ম্যাচিং সার্ভিস (fixes last_donation_date bug)
        $donors = app(DonorMatchingService::class)->match($bloodRequest);

        // 🚀 ৪. টার্গেটেড নোটিফিকেশন ব্যাকগ্রাউন্ড জবে পাঠানো (UI ব্লক হবে না)
        if ($donors->isNotEmpty()) {
            SendEmergencyBloodRequestNotificationJob::dispatch(
                bloodRequestId: $bloodRequest->id,
                districtName: $districtName,
                donorIds: $donors->pluck('id')->all()
            );
        }

        return redirect()->route('requests.show', $bloodRequest)
            ->with('success', 'আপনার রক্তের রিকোয়েস্টটি সফলভাবে তৈরি হয়েছে এবং ' . $donors->count() . ' জন ডোনারকে অ্যালার্ট পাঠানো হয়েছে।');
    }

    /**
     * রিকোয়েস্ট সম্পন্ন (Fulfilled) মার্ক করা
     * → DonationCompleted ইভেন্ট ফায়ার করা হয় প্রতিটি accepted ডোনারের জন্য
     */
    public function fulfill(Request $request, BloodRequest $bloodRequest)
    {
        Gate::authorize('markFulfilled', $bloodRequest);

        $bloodRequest->update(['status' => 'fulfilled']);

        // ─── সকল accepted ডোনারদের জন্য Event Fire করা ────────────────────
        $acceptedResponses = BloodRequestResponse::where('blood_request_id', $bloodRequest->id)
            ->where('status', 'accepted')
            ->with('user')
            ->get();

        foreach ($acceptedResponses as $response) {
            $donor = $response->user;
            if (!$donor) continue;

            // 🎯 First Responder Detection:
            // ইমার্জেন্সি রিকোয়েস্টে ৩ ঘণ্টার মধ্যে রেসপন্ড করলে বোনাস
            $isEmergency = ($bloodRequest->urgency === 'emergency');
            $responseTimeHours = $bloodRequest->created_at->diffInHours($response->created_at);
            $isFirstResponder  = $isEmergency && $responseTimeHours <= 3;
            $responseHour = $response->created_at->hour;
            $isMidnightSavior = $isEmergency && $responseHour >= 0 && $responseHour <= 5;

            // 🚀 ইভেন্ট ফায়ার — RewardDonorPoints Listener ব্যাকগ্রাউন্ডে চলবে
            event(new DonationCompleted(
                donor: $donor,
                bloodRequest: $bloodRequest,
                isEmergency: $isEmergency,
                isFirstResponder: $isFirstResponder,
                isMidnightSavior: $isMidnightSavior,
            ));
        }

        return back()->with('success', '🎉 অভিনন্দন! রিকোয়েস্টটি সম্পন্ন মার্ক করা হয়েছে এবং ডোনাররা পয়েন্ট পাবেন।');
    }

    public function renew(Request $request, BloodRequest $bloodRequest)
    {
        if ((int) $request->user()->id !== (int) $bloodRequest->requested_by) {
            abort(403, 'এই রিকোয়েস্ট রিনিউ করার অনুমতি আপনার নেই।');
        }

        $isRenewable = $bloodRequest->status === 'expired'
            || ($bloodRequest->needed_at && $bloodRequest->needed_at->lt(now()));

        if (! $isRenewable) {
            return back()->with('error', 'শুধুমাত্র এক্সপায়ারড বা সময় পেরিয়ে যাওয়া রিকোয়েস্ট রিনিউ করা যাবে।');
        }

        $validated = $request->validate([
            'needed_at' => ['required', 'date', 'after:now'],
            'urgency' => [
                'required',
                'string',
                'in:emergency,urgent,normal',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $neededAtInput = (string) $request->input('needed_at');

                    if ($neededAtInput === '') {
                        return;
                    }

                    try {
                        $neededAt = Carbon::parse($neededAtInput);
                    } catch (\Throwable $e) {
                        return;
                    }

                    if ($value === UrgencyLevel::EMERGENCY->value && $neededAt->gt(now()->addHours(24))) {
                        $fail('জরুরি (Emergency) রিকোয়েস্টের জন্য রক্তের প্রয়োজন অবশ্যই পরবর্তী ২৪ ঘণ্টার মধ্যে হতে হবে।');
                    }

                    if ($value === UrgencyLevel::URGENT->value && $neededAt->gt(now()->addHours(72))) {
                        $fail('আর্জেন্ট (Urgent) রিকোয়েস্টের জন্য সময় অবশ্যই পরবর্তী ৭২ ঘণ্টার মধ্যে হতে হবে।');
                    }
                },
            ],
        ]);

        $bloodRequest->update([
            'status' => 'pending',
            'needed_at' => Carbon::parse((string) $validated['needed_at']),
            'urgency' => $validated['urgency'],
        ]);

        return back()->with('success', 'আপনার রিকোয়েস্টটি সফলভাবে রিনিউ করা হয়েছে এবং ফিডে যুক্ত হয়েছে।');
    }

    /**
     * রিকোয়েস্টের বিস্তারিত এবং এক্সেপ্টেড ডোনার লিস্ট দেখানো
     */
    public function show(Request $request, BloodRequest $bloodRequest)
    {
        // 🚀 রিলেশনাল ডেটা লোড করা (JSON এরর ফিক্স করার জন্য)
        $bloodRequest->load([
            'requester:id,name',
            'responses.user:id,name,phone',
            'district:id,name',
            'upazila:id,name'
        ]);

        $accepted = $bloodRequest->responses->where('status', 'accepted')->values();
        $declined = $bloodRequest->responses->where('status', 'declined')->values();

        $canViewAcceptedDonors = $request->user()?->can('viewAcceptedDonors', $bloodRequest) ?? false;

        return view('requests.show', [
            'bloodRequest' => $bloodRequest,
            'acceptedCount' => $accepted->count(),
            'declinedCount' => $declined->count(),
            'acceptedResponses' => $canViewAcceptedDonors ? $accepted : collect(),
            'canViewAcceptedDonors' => $canViewAcceptedDonors,
        ]);
    }
}
