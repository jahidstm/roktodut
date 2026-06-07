<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpatialAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpatialHeatmapController extends Controller
{
    private const VALID_RANGES       = ['all_time', 'today', 'last_7_days', 'last_30_days'];
    private const VALID_BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

    public function data(Request $request, SpatialAnalyticsService $service): JsonResponse
    {
        $range = $request->query('range', 'all_time');
        if (!in_array($range, self::VALID_RANGES, true)) {
            $range = 'all_time';
        }

        $bloodGroup = $request->query('group');
        if ($bloodGroup && !in_array($bloodGroup, self::VALID_BLOOD_GROUPS, true)) {
            $bloodGroup = null;
        }

        $data = $service->getHeatmapData($range, $bloodGroup);
        return response()->json($data);
    }
}
