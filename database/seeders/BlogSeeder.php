<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HealthBlogMeta;
use App\Models\Post;
use App\Models\SuccessStoryMeta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BlogSeeder — Bangla demo content
 *
 * Seeds:
 *   - 4 default categories (upserted, idempotent)
 *   - 10 published Health Blog posts  + health_blog_meta + category attach
 *   - 10 published Success Story posts + success_story_meta + category attach
 *
 * Also removes the previous English demo posts (seeded slugs only).
 *
 * Prerequisites: LocationSeeder + DemoDonorsSeeder must have already run.
 */
class BlogSeeder extends Seeder
{
    // ══════════════════════════════════════════════════════════════════
    // Default categories (slugs kept stable; names/descriptions in Bangla)
    // ══════════════════════════════════════════════════════════════════
    private const DEFAULT_CATEGORIES = [
        [
            'name'        => 'রক্তদানের টিপস',
            'slug'        => 'blood-donation-tips',
            'description' => 'প্রথমবার ও নিয়মিত রক্তদাতাদের জন্য সহজ গাইড, প্রস্তুতি ও পরামর্শ।',
            'type'        => 'health',
            'sort_order'  => 1,
        ],
        [
            'name'        => 'স্বাস্থ্য ও বিজ্ঞান',
            'slug'        => 'health-and-science',
            'description' => 'রক্ত, পুষ্টি, হিমোগ্লোবিন ও নিরাপদ রক্তদান নিয়ে তথ্যভিত্তিক লেখা।',
            'type'        => 'health',
            'sort_order'  => 2,
        ],
        [
            'name'        => 'সাফল্যের গল্প',
            'slug'        => 'donor-success-stories',
            'description' => 'রক্তদানের মাধ্যমে জীবন বাঁচার বাস্তব অভিজ্ঞতা ও অনুপ্রেরণামূলক গল্প।',
            'type'        => 'story',
            'sort_order'  => 3,
        ],
        [
            'name'        => 'কমিউনিটি স্পটলাইট',
            'slug'        => 'community-spotlight',
            'description' => 'রক্তদূত কমিউনিটির উদ্যোগ, ক্যাম্পেইন, স্বেচ্ছাসেবী ও স্থানীয় গল্প।',
            'type'        => 'general',
            'sort_order'  => 4,
        ],
    ];

    /** Previous English demo titles (used only to compute and delete seeded slugs). */
    private const OLD_HEALTH_TITLES_EN = [
        "Why O+ Donors Are Bangladesh's Silent Heroes",
        '7 Science-Backed Benefits of Regular Blood Donation',
        'Understanding Blood Types and Compatibility',
        'How Blood Donation Naturally Lowers Your Iron Levels',
        'What Happens to Your Blood After You Donate?',
        'Nutrition Guide: What to Eat Before and After Donating',
        'Busting 5 Common Myths About Blood Donation',
        'The Role of Platelets in Emergency Medicine',
        'How Voluntary Donation Saves Lives in Rural Bangladesh',
        'Blood Donation and Cardiovascular Health: The Evidence',
    ];

    private const OLD_STORY_TITLES_EN = [
        "How a Stranger's Gift Saved My Daughter in Dhaka",
        "Against All Odds: A Thalassemia Survivor's Journey",
        'The Call at 2 AM That Changed My Life in Chittagong',
        'From Recipient to Donor: My Three-Year Journey',
        "A Mother's Prayer Answered by an Unknown Hero in Sylhet",
        'Road Accident, Rare Blood, and a City That Responded',
        'Paying It Forward: Why I Donate Every Four Months',
        'The Night Roktodut Connected Me with My Blood Match',
        'Surgery in Rajshahi: How 5 Donors Showed Up in 2 Hours',
        'My First Donation and the Letter That Made Me Cry',
    ];

    public function run(): void
    {
        // Faker used only for randomness (not for content language).
        $faker = \Faker\Factory::create('en_US');

        // ── Resolve author pool ────────────────────────────────────────
        $authorIds = User::pluck('id')->toArray();

        if (empty($authorIds)) {
            throw new \RuntimeException(
                'BlogSeeder requires existing users. Run LocationSeeder + DemoDonorsSeeder first.'
            );
        }

        // ══════════════════════════════════════════════════════════════
        // STEP 0 — Remove previous English demo posts (seeded slugs only)
        // ══════════════════════════════════════════════════════════════
        $this->command->info('🧹 Removing previous English demo blog posts...');

        $oldSlugs = [];
        foreach (self::OLD_HEALTH_TITLES_EN as $i => $t) {
            $oldSlugs[] = Str::slug($t) . '-' . ($i + 1);
        }
        foreach (self::OLD_STORY_TITLES_EN as $i => $t) {
            $oldSlugs[] = Str::slug($t) . '-story-' . ($i + 1);
        }

        $deleted = Post::whereIn('slug', $oldSlugs)->delete(); // cascades to meta + pivots
        $this->command->line("   Removed {$deleted} old demo post(s).");

        // ══════════════════════════════════════════════════════════════
        // STEP 1 — Seed / upsert the default categories
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('🏷️  Seeding default categories...');

        $categories = [];
        foreach (self::DEFAULT_CATEGORIES as $data) {
            $cat = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'type'        => $data['type'],
                    'sort_order'  => $data['sort_order'],
                    'is_active'   => true,
                ]
            );
            $categories[$data['slug']] = $cat;
            $this->command->line("   Category [{$cat->id}] \"{$cat->name}\" — type: {$cat->type}");
        }

        // Shorthand refs used when attaching below
        $catTips      = $categories['blood-donation-tips'];
        $catScience   = $categories['health-and-science'];
        $catStories   = $categories['donor-success-stories'];
        $catCommunity = $categories['community-spotlight'];

        $districtsBn = [
            'ঢাকা',
            'চট্টগ্রাম',
            'সিলেট',
            'রাজশাহী',
            'খুলনা',
            'বরিশাল',
            'রংপুর',
            'ময়মনসিংহ',
            'কুমিল্লা',
            'গাজীপুর',
            'নারায়ণগঞ্জ',
            'বগুড়া',
        ];

        // ══════════════════════════════════════════════════════════════
        // STEP 2 — Health Blog posts (Bangla)
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('🩺  Seeding 10 Bangla Health Blog posts...');

        $healthTitlesBn = [
            'রক্তদানের আগে যা জানা জরুরি: নতুন ডোনারের গাইড',
            'রক্তের গ্রুপ ও মিল: কার সাথে কার রক্ত যায়?',
            'রক্তদানের পর শরীরে কী পরিবর্তন হয়?',
            'রক্তদানের আগে ও পরে খাবার: সহজ ডায়েট গাইড',
            'রক্তদানের ৫টি ভ্রান্ত ধারণা: সত্যটা কী?',
            'হিমোগ্লোবিন, আয়রন ও রক্তদান: নিরাপদ থাকার টিপস',
            'প্লেটলেট দান: কখন দরকার এবং কীভাবে কাজ করে',
            'রমজান/উপবাসে রক্তদান: নিরাপদভাবে কী করবেন?',
            'গ্রামাঞ্চলে স্বেচ্ছা রক্তদানের গুরুত্ব: সচেতনতা কেন দরকার',
            'নিয়মিত রক্তদান: স্বাস্থ্য, মনোবল ও সামাজিক প্রভাব',
        ];

        $medicalReviewersBn = [
            'ডা. আরিফ হোসেন (এমবিবিএস, এফসিপিএস — হেমাটোলজি)',
            'ডা. নাসরিন সুলতানা (এমডি)',
            'ডা. কামাল উদ্দিন (এমবিবিএস, এমপিএইচ)',
            null,
        ];

        foreach ($healthTitlesBn as $i => $title) {
            $slug = 'bn-health-' . ($i + 1);

            $post = Post::where('slug', $slug)->first();

            if (! $post) {
                $post = Post::create([
                    'author_user_id' => $faker->randomElement($authorIds),
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $this->healthExcerptBn($faker),
                    'body_raw'       => $this->healthBodyRawBn($faker, $title),
                    'cover_image'    => null, // show UI placeholder (storage paths only)
                    'type'           => 'health',
                    'status'         => 'published',
                    'published_at'   => now()->subDays(rand(1, 180)),
                ]);

                HealthBlogMeta::create([
                    'post_id'               => $post->id,
                    'medically_reviewed_by' => $faker->randomElement($medicalReviewersBn),
                    'sources_json'          => json_encode([
                        [
                            'title'       => 'WHO: রক্তের নিরাপত্তা ও প্রাপ্যতা (ফ্যাক্টশিট)',
                            'url'         => 'https://www.who.int/news-room/fact-sheets/detail/blood-safety-and-availability',
                            'accessed_at' => now()->subDays(rand(10, 60))->toDateString(),
                        ],
                        [
                            'title'       => 'পাবমেড (PubMed): রক্তদান সম্পর্কিত গবেষণা সারাংশ',
                            'url'         => 'https://pubmed.ncbi.nlm.nih.gov/',
                            'accessed_at' => now()->subDays(rand(5, 30))->toDateString(),
                        ],
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                ]);

                $this->command->line("   Health #{$post->id}: {$title}");
            } else {
                $this->command->line("   Skipped (exists): {$slug}");
            }

            $categoryIds = ($i % 2 === 0)
                ? [$catTips->id, $catCommunity->id]
                : [$catScience->id, $catCommunity->id];

            $post->categories()->syncWithoutDetaching($categoryIds);
        }

        // ══════════════════════════════════════════════════════════════
        // STEP 3 — Success Story posts (Bangla)
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('❤️   Seeding 10 Bangla Success Story posts...');

        $storyTitlesBn = [
            'ঢাকায় এক রাতের ফোনকল—যেভাবে বাঁচল একটি জীবন',
            'চট্টগ্রামে অপারেশনের আগে রক্ত খোঁজা—রক্তদূত যেভাবে পাশে দাঁড়াল',
            'সিলেটে একজন অচেনা ডোনারের সাহায্যে মায়ের চোখের জল থামল',
            'রক্তগ্রহণকারী থেকে রক্তদাতা—আমার বদলে যাওয়ার গল্প',
            'দুর্ঘটনা, বিরল গ্রুপ, আর মানুষের ভালোবাসা',
            'প্রথম রক্তদান: ভয় থেকে গর্ব',
            'দুই ঘণ্টায় পাঁচ ডোনার—রাজশাহীর অবিশ্বাস্য সহযোগিতা',
            'একটি মেসেজ, একটি ব্যাগ রক্ত, তিনটি জীবন',
            'চার মাস পরপর কেন রক্ত দিই: অভ্যাস গড়ার গল্প',
            'রক্তদূত পরিবারের সাথে পরিচয়—মানবতার নতুন ঠিকানা',
        ];

        foreach ($storyTitlesBn as $i => $title) {
            $slug = 'bn-story-' . ($i + 1);

            $post = Post::where('slug', $slug)->first();

            if (! $post) {
                $district  = $faker->randomElement($districtsBn);
                $refTypes  = ['donation', 'blood_request', null];
                $refType   = $faker->randomElement($refTypes);
                $anonymize = $faker->randomElement(['public', 'initials', 'anonymous']);

                $post = Post::create([
                    'author_user_id' => $faker->randomElement($authorIds),
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $this->storyExcerptBn($faker),
                    'body_raw'       => $this->storyBodyRawBn($faker, $title, $district, $anonymize),
                    'cover_image'    => null,
                    'type'           => 'story',
                    'status'         => 'published',
                    'published_at'   => now()->subDays(rand(1, 180)),
                ]);

                SuccessStoryMeta::create([
                    'post_id'           => $post->id,
                    'district'          => $district,
                    'donation_ref_type' => $refType,
                    'donation_ref_id'   => $refType ? $faker->numberBetween(1, 50) : null,
                    'is_verified_story' => $faker->boolean(65),
                    'anonymize_level'   => $anonymize,
                ]);

                $this->command->line("   Story #{$post->id}: {$title}");
            } else {
                $this->command->line("   Skipped (exists): {$slug}");
            }

            $categoryIds = ($i % 3 === 0)
                ? [$catStories->id, $catCommunity->id, $catTips->id]
                : [$catStories->id, $catCommunity->id];

            $post->categories()->syncWithoutDetaching($categoryIds);
        }

        $this->command->info('');
        $this->command->info('✅ BlogSeeder complete (Bangla demo content).');
    }

    // ══════════════════════════════════════════════════════════════════
    // Bangla content generators
    // ══════════════════════════════════════════════════════════════════

    private function healthBodyRawBn(\Faker\Generator $faker, string $title): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $sentences = [
            'রক্তদান একটি নিরাপদ ও মানবিক কাজ—সঠিক প্রস্তুতি নিলে ঝুঁকি অনেকটাই কমে যায়।',
            'রক্তদানের আগে পর্যাপ্ত ঘুম, পানি এবং হালকা খাবার গুরুত্বপূর্ণ।',
            'রক্তদানের পর কয়েক মিনিট বিশ্রাম নিন এবং ভারী কাজ কিছুক্ষণ এড়িয়ে চলুন।',
            'জ্বর, সংক্রমণ, বা চিকিৎসকের নিষেধ থাকলে রক্তদান থেকে বিরত থাকুন।',
            'রক্তের গ্রুপ ও মিল (compatibility) জানা থাকলে জরুরি মুহূর্তে দ্রুত সাহায্য করা যায়।',
            'হিমোগ্লোবিন ও আয়রন ভালো রাখতে সুষম খাবার এবং নিয়মিত স্বাস্থ্য পরীক্ষা সহায়ক।',
        ];

        $tips = [
            'রক্তদানের আগে ২-৩ গ্লাস পানি পান করুন',
            'হালকা খাবার খান (ডিম/ডাল/ভাত/সবজি)',
            'ধূমপান ও অতিরিক্ত ক্যাফেইন এড়িয়ে চলুন',
            'রক্তদানের পরে ২৪ ঘণ্টা অতিরিক্ত পরিশ্রম করবেন না',
            'ইনজেকশনের জায়গায় ব্যথা/ফোলা হলে ঠান্ডা সেঁক দিন',
        ];

        $para1 = htmlspecialchars($this->pickMany($faker, $sentences, 2, 3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $para2 = htmlspecialchars($this->pickMany($faker, $sentences, 2, 3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $lis = $this->pickManyArray($faker, $tips, 3, 4);
        $lisHtml = implode('', array_map(
            fn($t) => '<li>' . htmlspecialchars($t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>',
            $lis
        ));

        return "<article>\n"
            . "<h1>{$safeTitle}</h1>\n"
            . "<h2>সংক্ষেপে কী বলছে বিষয়টি</h2>\n<p>{$para1}</p>\n"
            . "<h2>কীভাবে প্রস্তুতি নেবেন</h2>\n<ul>{$lisHtml}</ul>\n"
            . "<h2>রক্তদানের পরে যত্ন</h2>\n<p>{$para2}</p>\n"
            . "<p><strong>নোট:</strong> কোনো অসুস্থতা/ওষুধ সেবনের ক্ষেত্রে রক্তদানের আগে চিকিৎসকের পরামর্শ নিন।</p>\n"
            . "</article>";
    }

    private function storyBodyRawBn(
        \Faker\Generator $faker,
        string $title,
        string $district,
        string $anonymize
    ): string {
        $safeTitle    = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeDistrict = htmlspecialchars($district, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $namesBn = ['রাফি', 'তানজিলা', 'সাব্বির', 'নুসরাত', 'তামিম', 'মেহজাবিন', 'আকাশ', 'ফারহানা'];
        $narrator = match ($anonymize) {
            'initials'  => 'র. দ.',
            'anonymous' => 'একজন রক্তদাতা',
            default     => $faker->randomElement($namesBn),
        };
        $safeNarrator = htmlspecialchars($narrator, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $paraA = htmlspecialchars($this->pickMany($faker, [
            'সেদিন সন্ধ্যায় হঠাৎ করে একটি জরুরি কল আসে—রক্ত দরকার, সময় খুব কম।',
            'হাসপাতালের করিডোরে অপেক্ষা, দুশ্চিন্তা আর অনিশ্চয়তা—সব একসাথে চাপা পড়ে যায়।',
            'রক্তের গ্রুপ মিলছিল না, পরিচিত কেউ পাশে ছিল না—ঠিক তখনই রক্তদূতের কথা মনে পড়ে।',
        ], 2, 3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $paraB = htmlspecialchars($this->pickMany($faker, [
            'একটি পোস্ট/রিকোয়েস্ট দেওয়ার কিছুক্ষণের মধ্যেই কয়েকজন ডোনার যোগাযোগ করেন।',
            'যখন প্রথম ডোনারটি এসে বললেন “আমি আছি”—মনে হলো অন্ধকারে আলো জ্বলে উঠেছে।',
            'সব প্রক্রিয়া শেষ হতে বেশি সময় লাগেনি—কিন্তু সেই মুহূর্তটা আজও মনে গেঁথে আছে।',
        ], 2, 3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $paraC = htmlspecialchars($this->pickMany($faker, [
            'পরদিন সকালে বুঝলাম—একটি ব্যাগ রক্ত শুধু রক্ত নয়, এটা আশা।',
            'সেই অভিজ্ঞতার পর আমি নিয়মিত রক্তদাতা হওয়ার সিদ্ধান্ত নিই।',
            'আপনি হয়তো কাউকে চিনবেন না—তবু আপনার রক্ত কারো পুরো পৃথিবী বাঁচাতে পারে।',
        ], 2, 3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return "<article>\n"
            . "<h1>{$safeTitle}</h1>\n"
            . "<p><em>{$safeDistrict} থেকে শেয়ার করেছেন <strong>{$safeNarrator}</strong>।</em></p>\n"
            . "<h2>শুরুটা ছিল অস্থির</h2>\n<p>{$paraA}</p>\n"
            . "<h2>যখন সাহায্য এল</h2>\n<p>{$paraB}</p>\n"
            . "<h2>শেষ কথা</h2>\n<p>{$paraC}</p>\n"
            . "<p><strong>ধন্যবাদ</strong>—সব রক্তদাতাকে, যারা নিঃশব্দে জীবন বাঁচান।</p>\n"
            . "</article>";
    }

    private function healthExcerptBn(\Faker\Generator $faker): string
    {
        $openers = [
            'রক্তদানের আগে-পরের ছোট কিছু নিয়ম মানলেই অভিজ্ঞতাটি অনেক বেশি স্বস্তিদায়ক হয়।',
            'রক্তের গ্রুপ, হিমোগ্লোবিন আর পানিশূন্যতা—এই তিনটি বিষয় বুঝলেই প্রস্তুতি সহজ।',
            'প্রথমবার রক্ত দিতে ভয় লাগা স্বাভাবিক; সঠিক তথ্য জানলে ভয় অনেকটাই কমে যায়।',
            'রক্তদান নিরাপদ রাখতে এই টিপসগুলো আপনাকে সাহায্য করবে।',
        ];

        return $faker->randomElement($openers);
    }

    private function storyExcerptBn(\Faker\Generator $faker): string
    {
        $openers = [
            'অচেনা মানুষের সহানুভূতি কীভাবে এক মুহূর্তে সবকিছু বদলে দেয়—এটাই সেই গল্প।',
            'একটি জরুরি কল, কয়েকজন ডোনার, আর নতুন করে বেঁচে ওঠার আশা।',
            'রক্তদূতের মাধ্যমে পাওয়া সহযোগিতা—যা আজও কৃতজ্ঞতায় ভরিয়ে দেয়।',
            'এই অভিজ্ঞতা আমাকে নিয়মিত রক্তদাতা হতে অনুপ্রাণিত করেছে।',
        ];

        return $faker->randomElement($openers);
    }

    private function pickMany(\Faker\Generator $faker, array $items, int $min, int $max): string
    {
        $count = $faker->numberBetween($min, $max);
        $picked = $this->pickManyArray($faker, $items, $count, $count);
        return implode(' ', $picked);
    }

    private function pickManyArray(\Faker\Generator $faker, array $items, int $min, int $max): array
    {
        $count = $faker->numberBetween($min, $max);
        $count = max(1, min($count, count($items)));

        $keys = array_keys($items);
        shuffle($keys);

        $pickedKeys = array_slice($keys, 0, $count);
        return array_map(fn($k) => $items[$k], $pickedKeys);
    }
}
