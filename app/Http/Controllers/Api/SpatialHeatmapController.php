<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpatialAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpatialHeatmapController extends Controller
{
    private const VALID_RANGES = ['all_time', 'today', 'last_7_days', 'last_30_days'];

    public function data(Request $request, SpatialAnalyticsService $service): JsonResponse
    {
        $range = $request->query('range', 'all_time');
        if (!in_array($range, self::VALID_RANGES, true)) {
            $range = 'all_time';
        }
        $data = $service->getHeatmapData($range);
        return response()->json($data);
    }
}

