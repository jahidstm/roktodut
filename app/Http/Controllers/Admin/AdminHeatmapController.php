<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SpatialAnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminHeatmapController extends Controller
{
    private const VALID_RANGES = ['all_time', 'today', 'last_7_days', 'last_30_days'];

    public function __construct(private SpatialAnalyticsService $spatialService) {}

    public function index(Request $request)
    {
        $dateRange   = $this->resolveRange($request);
        $heatmapData = $this->spatialService->getHeatmapData($dateRange);

        return view('admin.analytics.heatmap', [
            'heatmapData'  => $heatmapData,
            'totalDemand'  => collect($heatmapData)->sum('demand'),
            'criticalCount'=> collect($heatmapData)->filter(fn($d) => $d['crs'] > 50)->count(),
            'generatedAt'  => now()->format('d M Y, h:i A'),
            'dateRange'    => $dateRange,
        ]);
    }

    /**
     * Stream a CSV download of the current filtered dataset.
     * Columns: District (EN), District (BN), Active Demand, Avg DFI, CRS Score, Emergency Level
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $dateRange   = $this->resolveRange($request);
        $heatmapData = $this->spatialService->getHeatmapData($dateRange);
        $districtMap = $this->spatialService->getDistrictMap();   // BN => EN
        $enToBn      = array_flip($districtMap);                  // EN => BN

        $rangeLabel  = match ($dateRange) {
            'today'        => 'today',
            'last_7_days'  => 'last_7_days',
            'last_30_days' => 'last_30_days',
            default        => 'all_time',
        };
        $fileName = "heatmap_export_{$rangeLabel}_" . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($heatmapData, $enToBn) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Bengali text correctly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'District (EN)',
                'District (BN)',
                'Active Demand',
                'Avg DFI',
                'CRS Score',
                'Emergency Level',
            ]);

            foreach ($heatmapData as $enName => $data) {
                fputcsv($handle, [
                    $enName,
                    $enToBn[$enName] ?? $enName,
                    $data['demand'],
                    $data['avg_dfi'],
                    $data['crs'],
                    $this->spatialService->getEmergencyLevel((float) $data['crs'], (int) $data['demand']),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    private function resolveRange(Request $request): string
    {
        $range = $request->query('range', 'all_time');
        return in_array($range, self::VALID_RANGES, true) ? $range : 'all_time';
    }
}

