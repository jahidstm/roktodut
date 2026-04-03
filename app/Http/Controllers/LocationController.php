<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * নির্দিষ্ট বিভাগের সব জেলা রিটার্ন করবে
     */
    public function getDistricts($division_id)
    {
        // ⚡ শুধু id এবং name আনা হচ্ছে পারফরম্যান্সের জন্য
        $districts = District::where('division_id', $division_id)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($districts);
    }

    /**
     * নির্দিষ্ট জেলার সব উপজেলা রিটার্ন করবে
     */
    public function getUpazilas($district_id)
    {
        $upazilas = Upazila::where('district_id', $district_id)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($upazilas);
    }
}
