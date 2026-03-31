<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // 🔍 ১. Public ফোল্ডার থেকে JSON রিড করা
        $jsonPath = public_path('data/bd_locations.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON File not found at: {$jsonPath}");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $locations = json_decode($jsonContent, true);

        $this->command->info('Parsing Nested Locations... Please wait.');

        // 🛡 ২. ট্রানজেকশন শুরু (ডেটাবেস ক্র্যাশ রোধ করতে)
        DB::transaction(function () use ($locations) {
            
            // ক্লিনআপ
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Upazila::truncate();
            District::truncate();
            Division::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 🚀 ৩. নেস্টেড পার্সিং (বিভাগ -> জেলা -> উপজেলা)
            foreach ($locations['divisions'] as $divisionName => $districts) {
                // বিভাগ তৈরি
                $division = Division::create([
                    'name' => $divisionName
                ]);

                foreach ($districts as $districtName => $upazilas) {
                    // জেলা তৈরি
                    $district = District::create([
                        'division_id' => $division->id,
                        'name'        => $districtName
                    ]);

                    // উপজেলা তৈরি (Array loop)
                    foreach ($upazilas as $upazilaName) {
                        Upazila::create([
                            'district_id' => $district->id,
                            'name'        => $upazilaName
                        ]);
                    }
                }
            }
        });

        $this->command->info('Location Data Seeded Successfully! ✅');
    }
}