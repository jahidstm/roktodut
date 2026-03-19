<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    /**
     * ক্যাশ থেকে বা ফাইল থেকে লোকেশন ডেটা রিটার্ন করবে
     */
    public function getLocations()
    {
        // ১. ক্যাশ ব্যবহার করা হয়েছে যাতে বারবার ফাইল রিড করতে না হয় (পারফরম্যান্স বুস্ট)
        $locations = Cache::rememberForever('bd_locations_data', function () {
            $path = public_path('data/bd_locations.json');

            if (!File::exists($path)) {
                return null;
            }

            // JSON ডেটাটিকে অ্যারেতে রূপান্তর করে ক্যাশ করা হচ্ছে
            return json_decode(File::get($path), true);
        });

        if (!$locations) {
            return response()->json(['error' => 'Location data not found.'], 404);
        }

        return response()->json($locations);
    }
}