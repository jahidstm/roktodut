<?php

namespace App\Http\Controllers;

use App\Models\Division;
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
        if (!is_numeric($division_id)) {
            $resolvedDivisionId = Division::where('name', $division_id)->value('id');
            if (!$resolvedDivisionId) {
                return response()->json([]);
            }
            $division_id = $resolvedDivisionId;
        }

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
        if (!is_numeric($district_id)) {
            $resolvedDistrictId = District::where('name', $district_id)->value('id');
            if (!$resolvedDistrictId) {
                return response()->json([]);
            }
            $district_id = $resolvedDistrictId;
        }

        $upazilas = Upazila::where('district_id', $district_id)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($upazilas);
    }
}
