<?php

namespace App\Services;

use App\Models\BloodRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SpatialAnalyticsService
{
    /**
     * 🗺️ GeoJSON Sanitization Guard
     *
     * আমাদের DB-তে district নামগুলো বাংলায় (যেমন: 'চট্টগ্রাম')।
     * Source GeoJSON: GADM 4.1 gadm41_BGD_2.json (ADM2 = District level, 64 features)
     * GeoJSON property key: "properties.NAME_2"
     *
     * All 64 values validated against live php inspect_geojson.php output.
     */
    private const DISTRICT_MAP = [
        // Chittagong Division
        'কুমিল্লা'         => 'Comilla',
        'ফেনী'             => 'Feni',
        'ব্রাহ্মণবাড়িয়া'  => 'Brahamanbaria',   // GADM spelling
        'রাঙ্গামাটি'       => 'Rangamati',
        'নোয়াখালী'        => 'Noakhali',
        'চাঁদপুর'          => 'Chandpur',
        'লক্ষ্মীপুর'       => 'Lakshmipur',
        'চট্টগ্রাম'        => 'Chittagong',
        'কক্সবাজার'        => "Cox'SBazar",       // GADM exact spelling
        'খাগড়াছড়ি'       => 'Khagrachhari',
        'বান্দরবান'        => 'Bandarban',

        // Rajshahi Division
        'সিরাজগঞ্জ'        => 'Sirajganj',
        'পাবনা'            => 'Pabna',
        'বগুড়া'           => 'Bogra',
        'রাজশাহী'          => 'Rajshahi',
        'নাটোর'            => 'Natore',
        'জয়পুরহাট'        => 'Joypurhat',
        'চাঁপাইনবাবগঞ্জ'   => 'Nawabganj',        // GADM uses 'Nawabganj'
        'নওগাঁ'            => 'Naogaon',

        // Khulna Division
        'যশোর'             => 'Jessore',
        'সাতক্ষীরা'        => 'Satkhira',
        'মেহেরপুর'         => 'Meherpur',
        'নড়াইল'           => 'Narail',
        'চুয়াডাঙ্গা'       => 'Chuadanga',
        'কুষ্টিয়া'        => 'Kushtia',
        'মাগুরা'           => 'Magura',
        'খুলনা'            => 'Khulna',
        'বাগেরহাট'         => 'Bagerhat',
        'ঝিনাইদহ'          => 'Jhenaidah',

        // Barisal Division
        'ঝালকাঠি'          => 'Jhalokati',
        'পটুয়াখালী'       => 'Patuakhali',
        'পিরোজপুর'         => 'Pirojpur',
        'বরিশাল'           => 'Barisal',
        'ভোলা'             => 'Bhola',
        'বরগুনা'           => 'Barguna',

        // Sylhet Division
        'সিলেট'            => 'Sylhet',
        'মৌলভীবাজার'      => 'Maulvibazar',       // GADM uses 'Maulvibazar'
        'হবিগঞ্জ'          => 'Habiganj',
        'সুনামগঞ্জ'        => 'Sunamganj',

        // Dhaka Division
        'নরসিংদী'          => 'Narsingdi',
        'গাজীপুর'          => 'Gazipur',
        'শরীয়তপুর'        => 'Shariatpur',
        'নারায়ণগঞ্জ'      => 'Narayanganj',
        'টাঙ্গাইল'         => 'Tangail',
        'কিশোরগঞ্জ'        => 'Kishoreganj',
        'মানিকগঞ্জ'        => 'Manikganj',
        'ঢাকা'             => 'Dhaka',
        'মুন্সিগঞ্জ'       => 'Munshiganj',
        'রাজবাড়ী'         => 'Rajbari',
        'মাদারীপুর'        => 'Madaripur',
        'গোপালগঞ্জ'        => 'Gopalganj',
        'ফরিদপুর'          => 'Faridpur',

        // Rangpur Division
        'পঞ্চগড়'          => 'Panchagarh',
        'দিনাজপুর'         => 'Dinajpur',
        'লালমনিরহাট'       => 'Lalmonirhat',
        'নীলফামারী'        => 'Nilphamari',
        'গাইবান্ধা'        => 'Gaibandha',
        'ঠাকুরগাঁও'        => 'Thakurgaon',
        'রংপুর'            => 'Rangpur',
        'কুড়িগ্রাম'       => 'Kurigram',

        // Mymensingh Division
        'শেরপুর'           => 'Sherpur',
        'ময়মনসিংহ'        => 'Mymensingh',
        'জামালপুর'         => 'Jamalpur',
        'নেত্রকোণা'        => 'Netrakona',         // GADM uses 'Netrakona'
    ];

    /**
     * হিটম্যাপের জন্য ডেটা তৈরি করে।
     *
     * @param string $dateRange  'all_time' | 'today' | 'last_7_days' | 'last_30_days'
     * @param string|null $bloodGroup  'A+' | 'B-' | ... | null (all groups)
     *
     * Cache Strategy (Synchronized):
     *   - Server cache: 3 minutes (180s)
     *   - Client auto-refresh: 3 minutes
     *   → ক্লায়েন্ট যখন hit করে, তখনই cache expire হয় → সত্যিকারের fresh data।
     */
    public function getHeatmapData(string $dateRange = 'all_time', ?string $bloodGroup = null): array
    {
        // Public route: cache the all_time result for exactly 3 minutes
        if ($dateRange === 'all_time') {
            $cacheKey = $bloodGroup
                ? "spatial_heatmap_data_{$bloodGroup}"
                : 'spatial_heatmap_data';

            return Cache::remember($cacheKey, now()->addMinutes(3), function () use ($bloodGroup) {
                return $this->computeHeatmapData('all_time', $bloodGroup);
            });
        }

        // Admin filtered views: skip cache — data must be fresh
        return $this->computeHeatmapData($dateRange, $bloodGroup);
    }

    private function computeHeatmapData(string $dateRange = 'all_time', ?string $bloodGroup = null): array
    {
        // ── 1. Date range filter ──────────────────────────────────────────
        $from = match ($dateRange) {
            'today'        => Carbon::today(),
            'last_7_days'  => Carbon::now()->subDays(7)->startOfDay(),
            'last_30_days' => Carbon::now()->subDays(30)->startOfDay(),
            default        => null,   // 'all_time' — no date filter
        };

        // ── 2. Blood group breakdown per district ─────────────────────────
        // Uses composite index: idx_br_heatmap (district_id, status, blood_group)
        $bgQuery = BloodRequest::whereIn('status', ['pending', 'in_progress'])
            ->join('districts', 'blood_requests.district_id', '=', 'districts.id')
            ->selectRaw('districts.name as district_name, blood_group, COUNT(*) as cnt')
            ->groupBy('districts.name', 'blood_group');

        if ($bloodGroup) {
            $bgQuery->where('blood_group', $bloodGroup);
        }

        if ($from) {
            $bgQuery->where('blood_requests.created_at', '>=', $from);
        }

        // Build: ['ঢাকা' => ['A+' => 3, 'B+' => 1], ...]
        $bgRows = $bgQuery->get();
        $bgByDistrict = [];
        foreach ($bgRows as $row) {
            $bgByDistrict[$row->district_name][$row->blood_group] = (int) $row->cnt;
        }

        // ── 3. Total demand per district (from the breakdown) ─────────────
        $demands = [];
        foreach ($bgByDistrict as $districtName => $groups) {
            $demands[$districtName] = array_sum($groups);
        }

        // ── 4. Pre-computed District Avg DFI — O(1) Redis Hash read ───────
        try {
            $avgDfiScores = Redis::hgetall('district_avg_dfi');
        } catch (\Exception $e) {
            Log::warning('[Heatmap] Redis unavailable for DFI fetch: ' . $e->getMessage());
            $avgDfiScores = [];
        }

        // ── 5. Normalization ──────────────────────────────────────────────
        $maxDemand = max(1, empty($demands) ? 1 : max($demands));
        $maxDfi    = 100;

        $heatmapData = [];

        foreach (self::DISTRICT_MAP as $dbName => $geoJsonName) {
            $demand   = (int) ($demands[$dbName] ?? 0);
            $avgDfi   = (float) ($avgDfiScores[$dbName] ?? 0.0);
            $bgGroups = $bgByDistrict[$dbName] ?? [];

            // ✅ demand = 0 হলে CRS সবসময় 0
            if ($demand === 0) {
                $crsScore = 0;
            } else {
                $normDemand = $demand / $maxDemand;
                $normDfi    = $avgDfi / $maxDfi;
                $crs        = ($normDemand * 0.6) + ($normDfi * 0.4);
                $crsScore   = round($crs * 100, 2);
            }

            // সর্বোচ্চ চাহিদার blood group বের করা
            $topBloodGroup = null;
            if (!empty($bgGroups)) {
                arsort($bgGroups);
                $topBloodGroup = array_key_first($bgGroups);
            }

            $heatmapData[$geoJsonName] = [
                'demand'          => $demand,
                'avg_dfi'         => round($avgDfi, 2),
                'crs'             => $crsScore,
                'blood_groups'    => $bgGroups,
                'top_blood_group' => $topBloodGroup,
            ];
        }

        return $heatmapData;
    }


    /**
     * Public accessor for DISTRICT_MAP (used by CSV export).
     */
    public function getDistrictMap(): array
    {
        return self::DISTRICT_MAP;   // BN => EN
    }

    /**
     * Human-readable Emergency Level from CRS score.
     */
    public function getEmergencyLevel(float $crs, int $demand): string
    {
        if ($demand === 0 || $crs === 0) return 'স্বাভাবিক (Safe)';
        if ($crs > 75)                   return 'সংকট (Critical)';
        if ($crs > 50)                   return 'জরুরি (High)';
        if ($crs > 30)                   return 'সতর্কতা (Warning)';
        return                                  'মনোযোগ (Elevated)';
    }
}
