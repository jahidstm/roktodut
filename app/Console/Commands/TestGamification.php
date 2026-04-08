<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BloodRequest;
use App\Models\PointLog;
use App\Events\DonationCompleted;
use App\Services\GamificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestGamification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:gamification';

    /**
     * The console command description.
     */
    protected $description = 'রক্তদূত গ্যামিফিকেশন সিস্টেম টেস্ট করার জন্য (পয়েন্ট, ব্যাজ ও ইভেন্ট)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 ব্লাড ডোনেশন গ্যামিফিকেশন টেস্ট শুরু হচ্ছে...');

        // ১. টেস্ট ইউজার তৈরি বা খুঁজে নেওয়া
        $testEmail = 'test_donor_' . Str::random(4) . '@example.com';
        $user = User::create([
            'name' => 'Test Donor',
            'email' => $testEmail,
            'password' => bcrypt('password'),
            'role' => 'donor',
            'blood_group' => 'O+',
            'points' => 0,
            'total_verified_donations' => 0,
            'is_onboarded' => true,
        ]);

        $this->info("✅ টেস্ট ইউজার তৈরি হয়েছে: {$user->email}");

        // ২. একটি ফেক ব্লাড রিকোয়েস্ট তৈরি
        $request = BloodRequest::create([
            'requested_by' => $user->id,
            'patient_name' => 'Demo Patient',
            'blood_group' => 'O+',
            'bags_needed' => 1,
            'division_id' => 1,
            'district_id' => 1,
            'upazila_id' => 1,
            'hospital' => 'Test Hospital',
            'contact_name' => 'Contact Person',
            'contact_number' => '01700000000',
            'needed_at' => now(),
            'status' => 'pending',
            'urgency' => 'emergency'
        ]);

        $this->info('✅ টেস্ট ব্লাড রিকোয়েস্ট তৈরি হয়েছে।');

        // ৩. ১ম ডোনেশন সিমুলেশন + "First Responder" বোনাস
        $this->warn('--- সিনারিও ১: ১ম ডোনেশন (Emergency & Fast Response) ---');
        
        // 🚀 সরাসরি সার্ভিস কল করছি যাতে Queue এর জন্য দেরি না হয়
        app(GamificationService::class)->processDonationReward($user, $request, true);

        $user->refresh();
        $this->checkStats($user, 60, 1, 'ব্রোঞ্জ (Bronze)');

        // ৪. মেটাডেটা ও পয়েন্ট লগ চেক
        $logCount = PointLog::where('user_id', $user->id)->count();
        $this->info("📊 পয়েন্ট লগ এন্ট্রি সংখ্যা: {$logCount}");

        // ৫. মাইলস্টোন টেস্ট (১০ বার ডোনেশন পর্যন্ত নিয়ে যাওয়া)
        $this->warn('--- সিনারিও ২: ১টি থেকে ৫টি ডোনেশনে উন্নীত করা ---');
        
        for($i = 0; $i < 4; $i++) {
            app(GamificationService::class)->processDonationReward($user, $request);
        }

        $user->refresh();
        $this->checkStats($user, 250, 5, 'সিলভার (Silver)');

        $this->info('--- ✅ টেস্ট সম্পন্ন হয়েছে! ---');
        
        // টেস্ট ডেটা পরিষ্কার করা (ঐচ্ছিক)
        if ($this->confirm('আপনি কি এই টেস্ট ইউজার ও লগগুলো ডিলিট করতে চান?')) {
            $user->badges()->detach();
            $user->pointLogs()->delete();
            $request->delete();
            $user->delete();
            $this->info('🧹 টেস্ট ডেটা পরিষ্কার করা হয়েছে।');
        }
    }

    private function checkStats($user, $expectedPoints, $expectedDonations, $badgeName)
    {
        $this->line("পয়েন্ট: {$user->points} (আশা করা হয়েছিল: >= {$expectedPoints})");
        $this->line("ডোনেশন: {$user->total_verified_donations} (আশা করা হয়েছিল: {$expectedDonations})");
        
        $hasBadge = $user->badges()->count() > 0;
        if($hasBadge) {
            $this->info("🏅 ইউজার ব্যাজ পেয়েছে! (মাইলস্টোন: {$badgeName})");
        } else {
            $this->error("❌ ইউজার কোনো ব্যাজ পায়নি!");
        }
    }
}
