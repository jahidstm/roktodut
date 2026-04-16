<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;
use App\Support\BanglaDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $analytics = Cache::remember('admin.analytics.dashboard.v1', now()->addMinutes(10), function () {
            $totalDonors = User::query()
                ->where('role', 'donor')
                ->count();

            $totalRecipients = User::query()
                ->where('role', 'recipient')
                ->count();

            $activeRequests = BloodRequest::query()
                ->where('status', 'pending')
                ->count();

            $successfulRequests = BloodRequest::query()
                ->where('status', 'fulfilled')
                ->count();

            $bloodGroupRows = User::query()
                ->select('blood_group', DB::raw('COUNT(*) as total'))
                ->where('role', 'donor')
                ->whereNotNull('blood_group')
                ->groupBy('blood_group')
                ->orderBy('blood_group')
                ->get();

            $bloodGroupDistribution = [
                'labels' => $bloodGroupRows->pluck('blood_group')->all(),
                'values' => $bloodGroupRows->pluck('total')->map(fn ($value) => (int) $value)->all(),
            ];

            $months = collect(range(11, 0, -1))
                ->map(fn ($i) => now()->startOfMonth()->subMonths($i))
                ->push(now()->startOfMonth())
                ->values();

            $monthlySuccessRows = DB::table('blood_request_responses')
                ->selectRaw("DATE_FORMAT(fulfilled_at, '%Y-%m') as ym, COUNT(DISTINCT blood_request_id) as total")
                ->whereNotNull('fulfilled_at')
                ->where('fulfilled_at', '>=', now()->startOfMonth()->subMonths(11))
                ->groupBy('ym')
                ->pluck('total', 'ym');

            $bnMonths = [
                1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
                5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
                9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
            ];

            $monthlyTrend = [
                'labels' => [],
                'values' => [],
                'keys' => [],
            ];

            foreach ($months as $month) {
                $key = $month->format('Y-m');
                $monthlyTrend['keys'][] = $key;
                $monthlyTrend['labels'][] = $bnMonths[(int) $month->format('n')] . ' ' . BanglaDate::digits($month->format('Y'));
                $monthlyTrend['values'][] = (int) ($monthlySuccessRows[$key] ?? 0);
            }

            return [
                'summary' => [
                    'total_donors' => $totalDonors,
                    'total_recipients' => $totalRecipients,
                    'active_requests' => $activeRequests,
                    'successful_requests' => $successfulRequests,
                    'lives_saved_estimate' => $successfulRequests * 3,
                ],
                'blood_group_distribution' => $bloodGroupDistribution,
                'monthly_success_trend' => $monthlyTrend,
            ];
        });

        return view('admin.analytics.index', compact('analytics'));
    }

    public function export(Request $request): StreamedResponse
    {
        $fileName = 'rokto_dut_analytics_export_' . now()->format('Y_m_d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Donor Analytics (Anonymized)']);
            fputcsv($handle, [
                'Donor ID',
                'Name',
                'Masked Email',
                'Masked Phone',
                'Blood Group',
                'District',
                'NID',
                'Total Verified Donations',
                'Points',
            ]);

            User::query()
                ->leftJoin('districts', 'districts.id', '=', 'users.district_id')
                ->where('users.role', 'donor')
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.blood_group',
                    'users.nid_number',
                    'users.total_verified_donations',
                    'users.points',
                    'districts.name as district_name',
                ])
                ->orderBy('users.id')
                ->cursor()
                ->each(function ($row) use ($handle) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $this->maskEmail((string) $row->email),
                        $this->maskPhone((string) $row->phone),
                        $row->blood_group,
                        $row->district_name,
                        $this->maskNid((string) $row->nid_number),
                        (int) ($row->total_verified_donations ?? 0),
                        (int) ($row->points ?? 0),
                    ]);
                });

            fputcsv($handle, []);
            fputcsv($handle, ['Request Analytics (Anonymized)']);
            fputcsv($handle, [
                'Request ID',
                'Blood Group',
                'Bags Needed',
                'Status',
                'Needed At',
                'Created At',
                'District',
                'Requester Name',
                'Requester Email (Masked)',
                'Requester Phone (Masked)',
            ]);

            BloodRequest::query()
                ->leftJoin('users', 'users.id', '=', 'blood_requests.requested_by')
                ->leftJoin('districts', 'districts.id', '=', 'blood_requests.district_id')
                ->select([
                    'blood_requests.id',
                    'blood_requests.blood_group',
                    'blood_requests.bags_needed',
                    'blood_requests.status',
                    'blood_requests.needed_at',
                    'blood_requests.created_at',
                    'districts.name as district_name',
                    'users.name as requester_name',
                    'users.email as requester_email',
                    'users.phone as requester_phone',
                ])
                ->orderBy('blood_requests.id')
                ->cursor()
                ->each(function ($row) use ($handle) {
                    fputcsv($handle, [
                        $row->id,
                        $row->blood_group,
                        (int) ($row->bags_needed ?? 0),
                        $row->status,
                        $row->needed_at,
                        $row->created_at,
                        $row->district_name,
                        $row->requester_name,
                        $this->maskEmail((string) $row->requester_email),
                        $this->maskPhone((string) $row->requester_phone),
                    ]);
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits) {
            return '';
        }

        if (strlen($digits) <= 6) {
            return substr($digits, 0, 2) . '***';
        }

        return substr($digits, 0, 5) . '***' . substr($digits, -3);
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return '';
        }

        [$name, $domain] = explode('@', $email, 2);
        $prefix = mb_substr($name, 0, 2);
        return $prefix . '***@' . $domain;
    }

    private function maskNid(string $nid): string
    {
        return trim($nid) === '' ? '' : 'সংরক্ষিত';
    }
}
