<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * ContactMessagesSeeder
 *
 * contact_messages টেবিলে ডেমো ডেটা ইনসার্ট করে।
 *
 * প্রতিটি স্ট্যাটাসের জন্য কমপক্ষে ১টি বার্তা:
 *   ✅ new         — নতুন, অপঠিত
 *   ✅ in_progress — প্রক্রিয়াধীন (অ্যাডমিন দেখেছেন)
 *   ✅ resolved    — সমাধান হয়েছে
 *   ✅ spam        — স্প্যাম/হানিপট ধরা
 *
 * FK: user_id → users.id (nullable; কিছু গেস্ট বার্তা ও কিছু লগইন ইউজারের)
 * idempotent: প্রতিবার seed করলেও ডুপ্লিকেট হবে না (truncate + insert pattern)
 */
class ContactMessagesSeeder extends Seeder
{
    public function run(): void
    {
        // ─── রেফারেন্স ইউজার লোড ────────────────────────────────────────────
        // DemoDonorsSeeder থেকে তৈরি হওয়া ডোনারদের ID ব্যবহার করবো।
        // প্রথম কয়েকজন donor ইউজার নেওয়া হচ্ছে।
        $donorUsers = User::where('role', UserRole::DONOR->value)
            ->whereNotNull('email_verified_at')
            ->limit(4)
            ->get();

        $donor1 = $donorUsers->get(0); // লগইন ইউজারের বার্তার জন্য
        $donor2 = $donorUsers->get(1);
        $donor3 = $donorUsers->get(2);

        // ─── আগের demo ডেটা মুছুন (idempotent) ─────────────────────────────
        // শুধু known demo emails মুছি, real data নষ্ট না করে
        ContactMessage::whereIn('email', [
            'rahim.demo@example.com',
            'sumaiya.demo@example.com',
            'karim.demo@example.com',
            'bot@spammer.xyz',
            'kamal.demo@example.com',
            'nusrat.demo@example.com',
            'farhan.demo@example.com',
        ])->delete();

        // ─── ১. স্ট্যাটাস: new (নতুন, অপঠিত) ───────────────────────────────
        ContactMessage::create([
            'user_id'    => $donor1?->id,
            'name'       => $donor1?->name ?? 'রাহিম হোসেন',
            'email'      => $donor1?->email ?? 'rahim.demo@example.com',
            'phone'      => '01711000001',
            'subject'    => 'প্রযুক্তিগত সমস্যার রিপোর্ট',
            'message'    => 'আমি গত দুই দিন ধরে প্ল্যাটফর্মে লগইন করতে পারছি না। পাসওয়ার্ড রিসেট করার পরেও একই সমস্যা হচ্ছে। অনুগ্রহ করে এই বিষয়টি দ্রুত সমাধান করুন।',
            'status'     => 'new',
            'ip_address' => '103.115.244.10',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0',
            'created_at' => Carbon::now()->subHours(3),
            'updated_at' => Carbon::now()->subHours(3),
        ]);

        ContactMessage::create([
            'user_id'    => null,
            'name'       => 'সুমাইয়া আক্তার',
            'email'      => 'sumaiya.demo@example.com',
            'phone'      => null,
            'subject'    => 'পরামর্শ ও ফিডব্যাক',
            'message'    => 'রক্তদূত অ্যাপটি ব্যবহার করে অনেক উপকার পেয়েছি। আমার পরামর্শ হলো ডোনার সার্চ ফিচারে বাড়তি ফিল্টার যোগ করলে আরও সহজ হতো, বিশেষ করে জেলা ও উপজেলা দিয়ে আলাদা করে সার্চ করার সুবিধা যদি থাকত।',
            'status'     => 'new',
            'ip_address' => '103.26.179.55',
            'user_agent' => 'Mozilla/5.0 (Linux; Android 13; SM-A536B) AppleWebKit/537.36 Chrome/123.0',
            'created_at' => Carbon::now()->subHours(1),
            'updated_at' => Carbon::now()->subHours(1),
        ]);

        // ─── ২. স্ট্যাটাস: in_progress (প্রক্রিয়াধীন) ─────────────────────
        ContactMessage::create([
            'user_id'    => $donor2?->id,
            'name'       => $donor2?->name ?? 'কারিম উদ্দিন',
            'email'      => $donor2?->email ?? 'karim.demo@example.com',
            'phone'      => '01831000002',
            'subject'    => 'NID ভেরিফিকেশন সমস্যা',
            'message'    => 'আমি আমার জাতীয় পরিচয়পত্র আপলোড করেছি প্রায় ৭ দিন আগে, কিন্তু এখনও "Verified" ব্যাজ পাইনি। আমার প্রোফাইল পেজে "Pending" দেখাচ্ছে। অনুগ্রহ করে আমার আবেদনটি পর্যালোচনা করুন।',
            'status'     => 'in_progress',
            'ip_address' => '202.4.96.101',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subHours(6),
        ]);

        ContactMessage::create([
            'user_id'    => null,
            'name'       => 'কামাল পারভেজ',
            'email'      => 'kamal.demo@example.com',
            'phone'      => '01921000003',
            'subject'    => 'রক্তের অনুরোধ সংক্রান্ত',
            'message'    => 'আমার বাবার জন্য জরুরি ভিত্তিতে O- রক্ত প্রয়োজন ছিল। রক্তদূতের মাধ্যমে অনুরোধ পাঠিয়েছিলাম কিন্তু কোনো ডোনার রেসপন্স করেননি। সিস্টেমে কোনো সমস্যা আছে কি? আমরা কি নিজে থেকে ডোনারদের সাথে যোগাযোগ করতে পারি?',
            'status'     => 'in_progress',
            'ip_address' => '59.152.96.200',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0',
            'created_at' => Carbon::now()->subDays(1)->subHours(4),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // ─── ৩. স্ট্যাটাস: resolved (সমাধান হয়েছে) ─────────────────────────
        ContactMessage::create([
            'user_id'    => $donor3?->id,
            'name'       => $donor3?->name ?? 'নুসরাত জাহান',
            'email'      => $donor3?->email ?? 'nusrat.demo@example.com',
            'phone'      => '01511000004',
            'subject'    => 'প্রযুক্তিগত সমস্যার রিপোর্ট',
            'message'    => 'আমার ড্যাশবোর্ডে "মোট ডোনেশন" কাউন্টার সঠিক দেখাচ্ছে না। আমি ৩ বার রক্ত দিয়েছি কিন্তু শুধু ১ টি দেখাচ্ছে। এটি কি ঠিক করা সম্ভব?',
            'status'     => 'resolved',
            'ip_address' => '117.18.232.45',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124.0',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        ContactMessage::create([
            'user_id'    => null,
            'name'       => 'ফারহান আহমেদ',
            'email'      => 'farhan.demo@example.com',
            'phone'      => null,
            'subject'    => 'অন্যান্য বিষয়',
            'message'    => 'রক্তদূত প্ল্যাটফর্মটি সত্যিই অসাধারণ উদ্যোগ। আমি একজন মেডিকেল ছাত্র এবং এই প্ল্যাটফর্মটি আমাদের ক্যাম্পাসে প্রচার করতে চাই। পার্টনারশিপের বিষয়ে কোনো তথ্য দিতে পারলে কৃতজ্ঞ হবো।',
            'status'     => 'resolved',
            'ip_address' => '182.160.105.233',
            'user_agent' => 'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 Chrome/123.0',
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(6),
        ]);

        // ─── ৪. স্ট্যাটাস: spam (বট/হানিপট ধরা) ────────────────────────────
        // এই বার্তাটি honeypot "website" ফিল্ড ভরা ছিল — controller এটিকে spam করেছে।
        ContactMessage::create([
            'user_id'    => null,
            'name'       => 'Auto Bot',
            'email'      => 'bot@spammer.xyz',
            'phone'      => null,
            'subject'    => 'অন্যান্য বিষয়',                          // বটেরও subject দরকার
            'message'    => 'Congratulations! You have been selected for a special offer. Click here: http://spam-link.example/win-prize-123',
            'status'     => 'spam',
            'ip_address' => '185.220.101.5',                           // Tor Exit Node IP (demo)
            'user_agent' => 'python-requests/2.31.0',                  // বট UA
            'created_at' => Carbon::now()->subDays(4)->subHours(2),
            'updated_at' => Carbon::now()->subDays(4)->subHours(2),
        ]);

        $this->command->info('✅ ContactMessagesSeeder: ৬টি ডেমো বার্তা — new×2, in_progress×2, resolved×2, spam×1 সিড সম্পন্ন।');
        $this->command->info('   ↳ FK integrity ঠিক রাখা হয়েছে (user_id nullable, শুধু বিদ্যমান ইউজারের ID ব্যবহার)।');
    }
}
