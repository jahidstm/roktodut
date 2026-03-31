<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // সব বিভাগ পাঠাবে
    public function getDivisions()
    {
        $divisions = Division::orderBy('name', 'asc')->get(['id', 'name']);
        return response()->json($divisions);
    }

    // নির্দিষ্ট বিভাগের জেলাগুলো পাঠাবে
    public function getDistricts($division_id)
    {
        $districts = District::where('division_id', $division_id)
                            ->orderBy('name', 'asc')
                            ->get(['id', 'name']);
        return response()->json($districts);
    }

    // নির্দিষ্ট জেলার উপজেলাগুলো পাঠাবে
    public function getUpazilas($district_id)
    {
        $upazilas = Upazila::where('district_id', $district_id)
                           ->orderBy('name', 'asc')
                           ->get(['id', 'name']);
        return response()->json($upazilas);
    }
}