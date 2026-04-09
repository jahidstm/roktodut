<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmergencyRadarSeeder extends Seeder
{
    public function run(): void
    {
        // প্রথম ডোনার ইউজারটি খুঁজে বের করো
        $donor = User::where('role', 'donor')
            ->whereNotNull('district_id')
            ->whereNotNull('blood_group')
            ->first();

        if (!$donor) {
            $this->command->warn('কোনো ডোনার ইউজার পাওয়া যায়নি। আগে DemoDonorsSeeder চালান।');
            return;
        }

        $districtId  = $donor->district_id;
        $divisionId  = $donor->division_id ?? 1;
        $upazilaId   = $donor->upazila_id ?? 1;
        $userGroup   = $donor->blood_group?->value ?? $donor->blood_group ?? 'B+';

        // অন্য একজন "রিকোয়েস্টকারী" ইউজার (কোনো admin বা অন্য donor)
        $requesterUser = User::where('id', '!=', $donor->id)
            ->whereNotNull('district_id')
            ->first() ?? $donor;

        $requests = [
            // ১. অতি জরুরি — ইউজারের নিজের ব্লাড গ্রুপ (CRITICAL MATCH)
            [
                'patient_name'   => 'রহিমা বেগম',
                'blood_group'    => $userGroup,
                'bags_needed'    => 2,
                'hospital'       => 'ঢাকা মেডিক্যাল কলেজ হাসপাতাল',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'করিম সাহেব',
                'contact_number' => '01711234567',
                'urgency'        => 'emergency',
                'needed_at'      => Carbon::now()->addHours(4),
                'status'         => 'pending',
                'notes'          => 'অপারেশনের জন্য জরুরি দরকার। যোগাযোগ করুন।',
            ],

            // ২. জরুরি — ভিন্ন গ্রুপ
            [
                'patient_name'   => 'আব্দুল করিম',
                'blood_group'    => 'A+',
                'bags_needed'    => 1,
                'hospital'       => 'স্যার সলিমুল্লাহ মেডিক্যাল কলেজ',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'নাজমা বেগম',
                'contact_number' => '01812345678',
                'urgency'        => 'urgent',
                'needed_at'      => Carbon::now()->addHours(8),
                'status'         => 'pending',
                'notes'          => 'ক্যান্সার রোগী, কেমোথেরাপির পরে রক্ত দরকার।',
            ],

            // ৩. জরুরি — ইউজারের নিজের ব্লাড গ্রুপ (2nd match)
            [
                'patient_name'   => 'মো. আলী হোসেন',
                'blood_group'    => $userGroup,
                'bags_needed'    => 3,
                'hospital'       => 'বঙ্গবন্ধু শেখ মুজিব মেডিক্যাল বিশ্ববিদ্যালয়',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'সালমা খাতুন',
                'contact_number' => '01912345678',
                'urgency'        => 'urgent',
                'needed_at'      => Carbon::now()->addHours(12),
                'status'         => 'pending',
                'notes'          => 'দুর্ঘটনায় আহত, জরুরি রক্তের প্রয়োজন।',
            ],

            // ৪. সাধারণ — O+ গ্রুপ
            [
                'patient_name'   => 'ফারহানা আক্তার',
                'blood_group'    => 'O+',
                'bags_needed'    => 1,
                'hospital'       => 'জেলা সদর হাসপাতাল',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'রফিকুল ইসলাম',
                'contact_number' => '01611234567',
                'urgency'        => 'normal',
                'needed_at'      => Carbon::now()->addDay(),
                'status'         => 'pending',
                'notes'          => 'প্রসব পরবর্তী রক্তক্ষরণের জন্য রক্ত দরকার।',
            ],

            // ৫. সাধারণ — AB+ গ্রুপ
            [
                'patient_name'   => 'জাহানারা বেগম',
                'blood_group'    => 'AB+',
                'bags_needed'    => 2,
                'hospital'       => 'কমিউনিটি হেলথ কেয়ার সেন্টার',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'আমিনুল হক',
                'contact_number' => '01511234567',
                'urgency'        => 'normal',
                'needed_at'      => Carbon::now()->addDays(2),
                'status'         => 'pending',
                'notes'          => 'হার্ট সার্জারির আগে রক্ত মজুত রাখতে চাই।',
            ],

            // ৬. অতি জরুরি — O- (Rare)
            [
                'patient_name'   => 'সাইফুল ইসলাম',
                'blood_group'    => 'O-',
                'bags_needed'    => 1,
                'hospital'       => 'ন্যাশনাল ইনস্টিটিউট অব কার্ডিওভাসকুলার ডিজিজেস',
                'division_id'    => $divisionId,
                'district_id'    => $districtId,
                'upazila_id'     => $upazilaId,
                'contact_name'   => 'নাসরিন পারভীন',
                'contact_number' => '01311234567',
                'urgency'        => 'emergency',
                'needed_at'      => Carbon::now()->addHours(2),
                'status'         => 'pending',
                'notes'          => 'O- অত্যন্ত বিরল। যেকোনো মূল্যে প্রয়োজন।',
            ],
        ];

        foreach ($requests as $data) {
            BloodRequest::create(array_merge($data, [
                'requested_by' => $requesterUser->id,
            ]));
        }

        $this->command->info("✅ {$districtId} জেলায় 6টি Emergency Radar ডেমো রিকোয়েস্ট তৈরি হয়েছে।");
        $this->command->info("   ইউজারের ব্লাড গ্রুপ ({$userGroup})-এর সাথে 2টি ম্যাচ করে।");
    }
}
