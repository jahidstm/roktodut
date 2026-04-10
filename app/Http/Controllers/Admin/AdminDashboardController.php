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
        $pendingClaims = BloodRequestResponse::with(['user', 'bloodRequest']) // 'user' হলো ডোনার
            ->whereIn('verification_status', ['claimed', 'disputed'])
            ->orderBy('donor_claimed_at', 'desc')
            ->get();

        // 🏅 ৫. পেন্ডিং NID ভেরিফিকেশন (System Admin Review)
        $pendingNids = User::where('nid_status', 'pending')
            ->whereNotNull('nid_path')
            ->with(['district', 'organization'])
            ->orderBy('updated_at', 'asc')
            ->get();

        // 📝 ৬. পেন্ডিং ব্লগ পোস্ট (Blog Moderation)
        $pendingBlogCount = Post::pendingReview()->count();

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
        ));
    }

    /**
     * সিস্টেম অ্যাডমিন NID অ্যাপ্রুভ / রিজেক্ট
     */
    public function verifyNid(Request $request, User $user): RedirectResponse
    {
        $decision = $request->input('decision'); // 'approve' | 'reject'

        if ($decision === 'approve') {
            $this->gamification->awardVerifiedBadge($user);
            return back()->with('success', "✅ {$user->name}-এর NID ভেরিফাই সম্পন্ন হয়েছে। 'Verified Donor' ব্যাজ যুক্ত হয়েছে।");
        }

        $this->gamification->revokeVerifiedBadge($user);
        return back()->with('error', "❌ {$user->name}-এর NID ভেরিফিকেশন বাতিল হয়েছে।");
    }
}
