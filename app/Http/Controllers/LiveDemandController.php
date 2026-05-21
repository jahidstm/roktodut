<?php

namespace App\Http\Controllers;

use App\Services\SpatialAnalyticsService;
use Illuminate\View\View;

class LiveDemandController extends Controller
{
    public function index(SpatialAnalyticsService $service): View
    {
        return view('live-demand.index');
    }
}
