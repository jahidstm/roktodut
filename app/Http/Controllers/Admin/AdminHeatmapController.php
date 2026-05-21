<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SpatialAnalyticsService;

class AdminHeatmapController extends Controller
{
    public function __construct(private SpatialAnalyticsService $spatialService) {}

    public function index()
    {
        // Pass raw heatmap data to the view (DFI, CRS visible to admin)
        $heatmapData = $this->spatialService->getHeatmapData();

        return view('admin.analytics.heatmap', [
            'heatmapData'  => $heatmapData,
            'totalDemand'  => collect($heatmapData)->sum('demand'),
            'criticalCount'=> collect($heatmapData)->filter(fn($d) => $d['crs'] > 50)->count(),
            'generatedAt'  => now()->format('d M Y, h:i A'),
        ]);
    }
}
