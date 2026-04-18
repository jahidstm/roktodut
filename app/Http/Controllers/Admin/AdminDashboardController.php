<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\Post;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly GamificationService $gamification) {}
    public function index()
    {
        // ১. গ্লোবাল কাউন্টস (Platform Health)
        $totalUsers = User::count();
        $totalDonors = User::where('role', 'donor')->count();
        $totalRequests = BloodRequest::count();
        $fulfilledRequests = BloodRequest::where('status', 'fulfilled')->count();

        // ২. সাকসেস রেট অ্যালগরিদম (Division by zero এড়ানোর জন্য সেফটি চেক)
        $successRate = $totalRequests > 0
            ? round(($fulfilledRequests / $totalRequests) * 100, 1)
            : 0;

        // ৩. ব্লাড গ্রুপ ডিমান্ড অ্যানালাইসিস (পাই-চার্টের জন্য - গত ৩০ দিন)
        $bloodGroupDemand = BloodRequest::select('blood_group', DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group')
            ->toArray();

        // ৪. লোকেশন-বেজড ইমার্জেন্সি ট্রেন্ড (বার-চার্টের জন্য টপ ৫ জেলা - গত ৩০ দিন)
        $districtDemandRaw = BloodRequest::with('district')
            ->select('district_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('district_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $districtDemand = [];
        foreach ($districtDemandRaw as $row) {
            $name = $row->district ? $row->district->name : 'অজানা জেলা';
            $districtDemand[$name] = $row->total;
        }

        // 🎯 ৫. যেসব ক্লেইম ভেরিফাই করার জন্য পেন্ডিং আছে বা ডিসপুট করা হয়েছে
        $pendingClaims = BloodRequestResponse::whereIn('verification_status', ['claimed', 'disputed'])->count();

        // 🏅 ৫. পেন্ডিং NID ভেরিফিকেশন (System Admin Review - Only org-less users)
        $pendingNids = User::where('nid_status', 'pending')
            ->whereNotNull('nid_path')
            ->whereNull('organization_id')
            ->count();

        // 🏢 ৭. Pending Organization Applications
        $pendingOrgs = \App\Models\Organization::where('status', 'pending')->count();

        // 📑 ৮. Recent Audit Logs
        $recentAuditLogs = \App\Models\AdminAuditLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // 🚨 ৯. Security Radar
        $todaysSecurityEventsCount = \App\Models\SecurityRadarEvent::whereDate('created_at', today())->count();
        $recentSecurityLogs = \App\Models\SecurityRadarEvent::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 📝 ৬. পেন্ডিং ব্লগ পোস্ট (Blog Moderation)
        $pendingBlogCount = Post::pendingReview()->count();

        // 📬 ১০. Support Inbox
        $pendingSupportMessages = \App\Models\ContactMessage::where('status', 'new')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDonors',
            'totalRequests',
            'fulfilledRequests',
            'successRate',
            'bloodGroupDemand',
            'districtDemand',
            'pendingClaims',
            'pendingNids',
            'pendingBlogCount',
            'pendingOrgs',
            'recentAuditLogs',
            'todaysSecurityEventsCount',
            'recentSecurityLogs',
            'pendingSupportMessages'
        ));
    }

    /**
     * 🛡️ Dedicated donation proof review queue
     */
    public function proofReviews()
    {
        $pendingClaims = BloodRequestResponse::with([
                'user',
                'bloodRequest.requester',
            ])
            ->whereIn('verification_status', ['claimed', 'disputed'])
            ->orderByDesc('donor_claimed_at')
            ->paginate(12)
            ->withQueryString();

        $reviewStats = [
            'total_pending' => BloodRequestResponse::whereIn('verification_status', ['claimed', 'disputed'])->count(),
            'claimed' => BloodRequestResponse::where('verification_status', 'claimed')->count(),
            'disputed' => BloodRequestResponse::where('verification_status', 'disputed')->count(),
        ];

        return view('admin.donations.proof-reviews', compact('pendingClaims', 'reviewStats'));
    }

    /**
     * 🪪 Dedicated NID verification queue
     */
    public function nidReviews()
    {
        $pendingNids = User::with('district')
            ->where('nid_status', 'pending')
            ->whereNotNull('nid_path')
            ->whereNull('organization_id')
            ->orderBy('updated_at', 'asc')
            ->paginate(15)
            ->withQueryString();

        $nidStats = [
            'total_pending' => User::where('nid_status', 'pending')
                ->whereNotNull('nid_path')
                ->whereNull('organization_id')
                ->count(),
            'approved' => User::where(function ($q) {
                $q->where('nid_status', 'approved')->orWhere('verified_badge', 1);
            })->count(),
        ];

        return view('admin.verification.nid-reviews', compact('pendingNids', 'nidStats'));
    }

    /**
     * 🏥 Dedicated organization/hospital verification queue
     */
    public function organizationReviews()
    {
        $pendingOrgs = \App\Models\Organization::with(['locationDistrict', 'locationUpazila'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(12)
            ->withQueryString();

        $orgStats = [
            'total_pending' => \App\Models\Organization::where('status', 'pending')->count(),
            'approved' => \App\Models\Organization::where('status', 'approved')->count(),
            'rejected' => \App\Models\Organization::where('status', 'rejected')->count(),
        ];

        return view('admin.verification.organization-reviews', compact('pendingOrgs', 'orgStats'));
    }

    /**
     * Helper to log admin audit
     */
    private function logAudit(string $actionType, $targetId, string $targetType, array $details = [])
    {
        \App\Models\AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'action_type' => $actionType,
            'target_id' => $targetId,
            'target_type' => $targetType,
            'details' => $details,
        ]);
    }

    /**
     * সিস্টেম অ্যাডমিন NID অ্যাপ্রুভ / রিজেক্ট
     */
    public function verifyNid(Request $request, User $user): RedirectResponse
    {
        $decision = $request->input('decision'); // 'approve' | 'reject'

        if ($decision === 'approve') {
            $this->gamification->awardVerifiedBadge($user);
            $this->logAudit('nid_approve', $user->id, User::class);
            return back()->with('success', "✅ {$user->name}-এর NID ভেরিফাই সম্পন্ন হয়েছে। 'Verified Donor' ব্যাজ যুক্ত হয়েছে।");
        }

        $this->gamification->revokeVerifiedBadge($user);
        
        // Log to security radar if repeated rejections (e.g. > 2 times)
        $rejectionCount = \App\Models\AdminAuditLog::where('target_id', $user->id)
            ->where('target_type', User::class)
            ->where('action_type', 'nid_reject')
            ->count();
            
        if ($rejectionCount >= 2) {
            \App\Models\SecurityRadarEvent::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'event_type' => 'repeated_nid_rejection',
                'description' => "ইউজারের NID " . ($rejectionCount + 1) . " বার রিজেক্ট করা হয়েছে।",
            ]);
        }

        $this->logAudit('nid_reject', $user->id, User::class);
        return back()->with('error', "❌ {$user->name}-এর NID ভেরিফিকেশন বাতিল হয়েছে।");
    }

    /**
     * অর্গানাইজেশন অ্যাপ্রুভ / রিজেক্ট
     */
    public function verifyOrg(Request $request, \App\Models\Organization $organization): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|max:500'
        ]);

        $status = $request->input('status');
        
        $organization->status = $status;
        $organization->reviewed_by = auth()->id();
        $organization->reviewed_at = now();

        if ($status === 'approved') {
            $organization->is_verified = true;
            $organization->rejection_reason = null;
            $organization->save();

            // Set the creator as Org Admin via intermediate table
            $organization->members()->syncWithoutDetaching([
                $organization->admin_id => ['status' => 'approved']
            ]);

            $this->logAudit('org_approve', $organization->id, \App\Models\Organization::class);
            return back()->with('success', "✅ {$organization->name} সফলভাবে অ্যাপ্রুভ করা হয়েছে।");
        } else {
            $organization->is_verified = false;
            $organization->rejection_reason = $request->input('rejection_reason');
            $organization->save();
            
            $this->logAudit('org_reject', $organization->id, \App\Models\Organization::class, [
                'reason' => $request->input('rejection_reason')
            ]);
            return back()->with('error', "❌ {$organization->name} এর আবেদন বাতিল করা হয়েছে।");
        }
    }

    /**
     * View Organization document securely
     */
    public function viewOrgDocument(Request $request, \App\Models\Organization $organization)
    {
        if (!$organization->document_path) {
            abort(404, 'ডকুমেন্ট আপলোড করা হয়নি।');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($organization->document_path)) {
            abort(404, 'ফাইলটি সার্ভারে পাওয়া যায়নি।');
        }

        return \Illuminate\Support\Facades\Storage::disk('private')->response($organization->document_path);
    }
}
