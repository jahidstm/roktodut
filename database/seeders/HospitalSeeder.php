<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            // ─── ঢাকা বিভাগ (district_id: 1 = Dhaka) ──────────────
            [
                'name'        => 'Dhaka Medical College Hospital',
                'name_bn'     => 'ঢাকা মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['DMCH', 'Dhaka Medical', 'D.M.C.H', 'ঢাকা মেডিকেল', 'DMC Hospital'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Bangabandhu Sheikh Mujib Medical University',
                'name_bn'     => 'বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয়',
                'aliases'     => ['BSMMU', 'PG Hospital', 'পিজি হসপিটাল', 'Shahbag Hospital', 'IPGMR'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Sir Salimullah Medical College & Mitford Hospital',
                'name_bn'     => 'স্যার সলিমুল্লাহ মেডিকেল কলেজ ও মিটফোর্ড হাসপাতাল',
                'aliases'     => ['Mitford Hospital', 'SSMC', 'মিটফোর্ড', 'মিডফোর্ড'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'National Institute of Cardiovascular Diseases',
                'name_bn'     => 'জাতীয় হৃদরোগ ইনস্টিটিউট',
                'aliases'     => ['NICVD', 'National Heart Foundation', 'হৃদরোগ ইনস্টিটিউট', 'Heart Hospital Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'National Institute of Neurosciences',
                'name_bn'     => 'জাতীয় নিউরোসায়েন্স ইনস্টিটিউট',
                'aliases'     => ['NINS', 'নিউরোসায়েন্স', 'Neuroscience Hospital Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Shohrawardi Medical College Hospital',
                'name_bn'     => 'শহীদ সোহরাওয়ার্দী মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['Suhrawardy Hospital', 'Sohrawardy', 'সোহরাওয়ার্দী', 'SMC'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Square Hospital',
                'name_bn'     => 'স্কয়ার হাসপাতাল',
                'aliases'     => ['Square', 'Square Dhaka', 'স্কয়ার'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Evercare Hospital Dhaka',
                'name_bn'     => 'এভারকেয়ার হাসপাতাল ঢাকা',
                'aliases'     => ['Apollo Hospital', 'Evercare', 'এভারকেয়ার', 'Apollo Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'United Hospital',
                'name_bn'     => 'ইউনাইটেড হাসপাতাল',
                'aliases'     => ['United', 'United Dhaka', 'ইউনাইটেড'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Ibn Sina Hospital',
                'name_bn'     => 'ইবনে সিনা হাসপাতাল',
                'aliases'     => ['Ibn Sina', 'Ibne Sina', 'ইবনে সিনা', 'Ibn Sina Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Labaid Specialized Hospital',
                'name_bn'     => 'ল্যাবএইড হাসপাতাল',
                'aliases'     => ['Labaid', 'Lab Aid', 'ল্যাবএইড', 'Labaid Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Popular Diagnostic Centre',
                'name_bn'     => 'পপুলার হাসপাতাল',
                'aliases'     => ['Popular Hospital', 'Popular', 'পপুলার'],
                'district_id' => 1,
                'is_verified' => true,
            ],

            // ─── চট্টগ্রাম বিভাগ (district_id: 20 = Chittagong) ──
            [
                'name'        => 'Chittagong Medical College Hospital',
                'name_bn'     => 'চট্টগ্রাম মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['CMCH', 'Chittagong Medical', 'চট্টগ্রাম মেডিকেল', 'CMC Hospital'],
                'district_id' => 20,
                'is_verified' => true,
            ],
            [
                'name'        => 'Chevron Clinical Laboratory',
                'name_bn'     => 'শেভরন ক্লিনিক্যাল ল্যাবরেটরি',
                'aliases'     => ['Chevron', 'শেভরন', 'Chevron Chittagong'],
                'district_id' => 20,
                'is_verified' => true,
            ],

            // ─── রাজশাহী বিভাগ (district_id: 41 = Rajshahi) ────────
            [
                'name'        => 'Rajshahi Medical College Hospital',
                'name_bn'     => 'রাজশাহী মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['RMCH', 'Rajshahi Medical', 'রাজশাহী মেডিকেল', 'RMC'],
                'district_id' => 41,
                'is_verified' => true,
            ],

            // ─── খুলনা বিভাগ (district_id: 47 = Khulna) ─────────────
            [
                'name'        => 'Khulna Medical College Hospital',
                'name_bn'     => 'খুলনা মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['KMCH', 'Khulna Medical', 'খুলনা মেডিকেল', 'KMC'],
                'district_id' => 47,
                'is_verified' => true,
            ],

            // ─── সিলেট বিভাগ (district_id: 57 = Sylhet) ──────────────
            [
                'name'        => 'Sylhet MAG Osmani Medical College Hospital',
                'name_bn'     => 'সিলেট এমএজি ওসমানী মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['Osmani Hospital', 'Osmani Medical', 'ওসমানী হাসপাতাল', 'SOMC', 'Sylhet Medical'],
                'district_id' => 57,
                'is_verified' => true,
            ],

            // ─── বরিশাল বিভাগ (district_id: 10 = Barisal) ────────────
            [
                'name'        => 'Sher-E-Bangla Medical College Hospital',
                'name_bn'     => 'শেরে বাংলা মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['SBMCH', 'Barisal Medical', 'শেরে বাংলা', 'Barishal Medical', 'SBM'],
                'district_id' => 10,
                'is_verified' => true,
            ],

            // ─── রংপুর বিভাগ (district_id: 55 = Rangpur) ─────────────
            [
                'name'        => 'Rangpur Medical College Hospital',
                'name_bn'     => 'রংপুর মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['RMCH Rangpur', 'Rangpur Medical', 'রংপুর মেডিকেল', 'RMC Rangpur'],
                'district_id' => 55,
                'is_verified' => true,
            ],

            // ─── ময়মনসিংহ বিভাগ (district_id: 34 = Mymensingh) ──────
            [
                'name'        => 'Mymensingh Medical College Hospital',
                'name_bn'     => 'ময়মনসিংহ মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['MMCH', 'Mymensingh Medical', 'ময়মনসিংহ মেডিকেল', 'MMC'],
                'district_id' => 34,
                'is_verified' => true,
            ],

            // ─── ঢাকার অতিরিক্ত বিশেষায়িত হসপিটাল ─────────────────
            [
                'name'        => 'National Orthopaedic Hospital and Rehabilitation Institute',
                'name_bn'     => 'জাতীয় অর্থোপেডিক হাসপাতাল ও পুনর্বাসন প্রতিষ্ঠান',
                'aliases'     => ['NOHRI', 'Bone Hospital Dhaka', 'Pangu Hospital', 'পঙ্গু হাসপাতাল'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'National Cancer Institute and Hospital',
                'name_bn'     => 'জাতীয় ক্যান্সার ইনস্টিটিউট',
                'aliases'     => ['Cancer Hospital', 'NCIH', 'ক্যান্সার হাসপাতাল', 'National Cancer Hospital Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'National Institute of Kidney Diseases and Urology',
                'name_bn'     => 'জাতীয় কিডনি ইনস্টিটিউট',
                'aliases'     => ['NIKDU', 'Kidney Hospital Dhaka', 'কিডনি হাসপাতাল'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'National Eye Care',
                'name_bn'     => 'জাতীয় চক্ষু বিজ্ঞান ইনস্টিটিউট',
                'aliases'     => ['Eye Hospital Dhaka', 'চক্ষু হাসপাতাল', 'NIOC', 'National Eye Hospital'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Kurmitola General Hospital',
                'name_bn'     => 'কুর্মিটোলা জেনারেল হাসপাতাল',
                'aliases'     => ['Kurmitola Hospital', 'কুর্মিটোলা', 'Cantonment Hospital Dhaka'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Mugda Medical College and Hospital',
                'name_bn'     => 'মুগদা মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['Mugda Hospital', 'মুগদা', 'Mugda General Hospital'],
                'district_id' => 1,
                'is_verified' => true,
            ],
            [
                'name'        => 'Anwer Khan Modern Medical College Hospital',
                'name_bn'     => 'আনোয়ার খান মডার্ন মেডিকেল কলেজ হাসপাতাল',
                'aliases'     => ['Anwer Khan Modern', 'Modern Hospital Dhanmondi', 'আনোয়ার খান'],
                'district_id' => 1,
                'is_verified' => true,
            ],
        ];

        foreach ($hospitals as $data) {
            Hospital::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $this->command->info('✅ ' . count($hospitals) . '  hospitals seeded successfully!');
    }
}
