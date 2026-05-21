<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpatialAnalyticsService;
use Illuminate\Http\JsonResponse;

class SpatialHeatmapController extends Controller
{
    public function data(SpatialAnalyticsService $service): JsonResponse
    {
        $data = $service->getHeatmapData();
        return response()->json($data);
    }
}
