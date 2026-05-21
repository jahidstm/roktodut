<?php

namespace App\Services;

use App\Models\BloodRequest;
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
     * Cache: 15 মিনিট (ভারী কোয়েরি রিপিট করে না)
     */
    public function getHeatmapData(): array
    {
        return Cache::remember('spatial_heatmap_data', now()->addMinutes(15), function () {
            return $this->computeHeatmapData();
        });
    }

    private function computeHeatmapData(): array
    {
        // 1. Active emergency demand — district অনুযায়ী গ্রুপ
        $demands = BloodRequest::whereIn('status', ['pending', 'in_progress'])
            ->join('districts', 'blood_requests.district_id', '=', 'districts.id')
            ->selectRaw('districts.name as district_name, COUNT(*) as demand_count')
            ->groupBy('districts.name')
            ->pluck('demand_count', 'district_name')
            ->toArray();

        // 2. Pre-computed District Avg DFI — O(1) Redis Hash read
        try {
            $avgDfiScores = Redis::hgetall('district_avg_dfi');
        } catch (\Exception $e) {
            Log::warning('[Heatmap] Redis unavailable for DFI fetch: ' . $e->getMessage());
            $avgDfiScores = [];
        }

        // 3. Normalization
        $maxDemand = max(1, empty($demands) ? 1 : max($demands));
        $maxDfi    = 100; // DFI always capped at 100

        $heatmapData = [];

        // GeoJSON-এর সব district কভার করতে DISTRICT_MAP iterate করি
        foreach (self::DISTRICT_MAP as $dbName => $geoJsonName) {
            $demand = (int) ($demands[$dbName] ?? 0);
            $avgDfi = (float) ($avgDfiScores[$dbName] ?? 0.0);

            // ✅ False Warning Fix: demand = 0 হলে CRS সবসময় 0 (Safe Green)
            if ($demand === 0) {
                $crsScore = 0;
            } else {
                $normDemand = $demand / $maxDemand;
                $normDfi    = $avgDfi / $maxDfi;

                // CRS Formula: Demand-weighted (0.6) + DFI-weighted (0.4)
                $crs      = ($normDemand * 0.6) + ($normDfi * 0.4);
                $crsScore = round($crs * 100, 2);
            }

            $heatmapData[$geoJsonName] = [
                'demand'  => $demand,
                'avg_dfi' => round($avgDfi, 2),
                'crs'     => $crsScore,
            ];
        }

        return $heatmapData;
    }
}
