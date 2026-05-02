<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private const TYPE_MAP = [
        'blood_request' => BloodRequest::class,
        'user' => User::class,
        'organization' => Organization::class,
        BloodRequest::class => BloodRequest::class,
        User::class => User::class,
        Organization::class => Organization::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string', 'in:' . implode(',', array_keys(self::TYPE_MAP))],
            'reportable_id' => ['required', 'integer', 'min:1'],
            'category' => ['required', 'in:fake_info,harassment,spam,inappropriate,other'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $reportableClass = self::TYPE_MAP[$validated['reportable_type']];
        $reportable = $reportableClass::query()->find($validated['reportable_id']);

        if (! $reportable) {
            return back()->withInput()->with('error', 'যে কন্টেন্টটি রিপোর্ট করতে চাচ্ছেন সেটি পাওয়া যায়নি।');
        }

        $user = $request->user();

        Report::create([
            'reportable_type' => $reportableClass,
            'reportable_id' => $reportable->getKey(),
            'category' => $validated['category'],
            'message' => $validated['message'] ?? null,
            'reporter_type' => $user ? 'user' : 'guest',
            'reporter_user_id' => $user?->id,
            'reporter_ip_hash' => hash('sha256', ((string) $request->ip()) . '|' . ((string) config('app.key'))),
            'status' => 'open',
        ]);

        return back()->with('success', 'ধন্যবাদ। রিপোর্টটি জমা হয়েছে এবং অ্যাডমিন টিম রিভিউ করবে।');
    }
}
