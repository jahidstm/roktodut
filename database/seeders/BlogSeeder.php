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
 * BlogSeeder — v2 (with taxonomy support)
 *
 * Seeds:
 *   - 4 default categories (upserted, idempotent)
 *   - 10 published Health Blog posts  + health_blog_meta + category attach
 *   - 10 published Success Story posts + success_story_meta + category attach
 *
 * Prerequisites: LocationSeeder + DemoDonorsSeeder must have already run.
 * The seeder is fully idempotent: every post is guarded by its slug.
 */
class BlogSeeder extends Seeder
{
    // ══════════════════════════════════════════════════════════════════
    // 4 Default categories (PRD-defined)
    // ══════════════════════════════════════════════════════════════════
    private const DEFAULT_CATEGORIES = [
        [
            'name'        => 'Blood Donation Tips',
            'slug'        => 'blood-donation-tips',
            'description' => 'Practical guides and tips for first-time and regular blood donors.',
            'type'        => 'health',
            'sort_order'  => 1,
        ],
        [
            'name'        => 'Health & Science',
            'slug'        => 'health-and-science',
            'description' => 'Medically reviewed articles on haematology, nutrition, and donor wellness.',
            'type'        => 'health',
            'sort_order'  => 2,
        ],
        [
            'name'        => 'Donor Success Stories',
            'slug'        => 'donor-success-stories',
            'description' => 'Real stories of lives saved through voluntary blood donation.',
            'type'        => 'story',
            'sort_order'  => 3,
        ],
        [
            'name'        => 'Community Spotlight',
            'slug'        => 'community-spotlight',
            'description' => 'Highlights from the Roktodut donor community across Bangladesh.',
            'type'        => 'general',
            'sort_order'  => 4,
        ],
    ];

    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');

        // ── Resolve author pool ────────────────────────────────────────
        $authorIds = User::pluck('id')->toArray();

        if (empty($authorIds)) {
            throw new \RuntimeException(
                'BlogSeeder requires existing users. Run LocationSeeder + DemoDonorsSeeder first.'
            );
        }

        // ══════════════════════════════════════════════════════════════
        // STEP 1 — Seed / upsert the 4 default categories
        // ══════════════════════════════════════════════════════════════
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

        // ══════════════════════════════════════════════════════════════
        // STEP 2 — Cover images + districts
        // ══════════════════════════════════════════════════════════════
        $coverImages = [
            'https://picsum.photos/seed/blood1/1200/630',
            'https://picsum.photos/seed/health2/1200/630',
            'https://picsum.photos/seed/donor3/1200/630',
            'https://picsum.photos/seed/story4/1200/630',
            'https://picsum.photos/seed/life5/1200/630',
            'https://picsum.photos/seed/care6/1200/630',
            'https://picsum.photos/seed/hope7/1200/630',
            'https://picsum.photos/seed/save8/1200/630',
        ];

        $districts = [
            'Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi',
            'Khulna', 'Barisal', 'Rangpur', 'Mymensingh',
            'Comilla', 'Gazipur', 'Narayanganj', 'Bogura',
        ];

        // ══════════════════════════════════════════════════════════════
        // STEP 3 — Health Blog posts
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('🩺  Seeding 10 Health Blog posts...');

        $healthTitles = [
            'Why O+ Donors Are Bangladesh\'s Silent Heroes',
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

        $medicalReviewers = [
            'Dr. Arif Hossain, MBBS, FCPS (Hematology)',
            'Dr. Nasrin Sultana, MD',
            'Dr. Kamal Uddin, MBBS, MPH',
            null, // articles pending peer review
        ];

        foreach ($healthTitles as $i => $title) {
            $slug = Str::slug($title) . '-' . ($i + 1);

            // Idempotent guard
            $post = Post::where('slug', $slug)->first();

            if (! $post) {
                $post = Post::create([
                    'author_user_id' => $faker->randomElement($authorIds),
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $this->healthExcerpt($faker),
                    'body_raw'       => $this->rawMarkdown($faker),
                    'body_sanitized' => $this->sanitizedHealthHtml($faker, $title),
                    'cover_image'    => $faker->randomElement($coverImages),
                    'type'           => 'health',
                    'status'         => 'published',
                    'published_at'   => now()->subDays(rand(1, 180)),
                ]);

                HealthBlogMeta::create([
                    'post_id'               => $post->id,
                    'medically_reviewed_by' => $faker->randomElement($medicalReviewers),
                    'sources_json'          => json_encode([
                        [
                            'title'       => 'WHO Blood Safety and Availability: Factsheet',
                            'url'         => 'https://www.who.int/news-room/fact-sheets/detail/blood-safety-and-availability',
                            'accessed_at' => now()->subDays(rand(10, 60))->toDateString(),
                        ],
                        [
                            'title'       => $faker->sentence(5),
                            'url'         => 'https://pubmed.ncbi.nlm.nih.gov/' . $faker->numberBetween(10000000, 39999999),
                            'accessed_at' => now()->subDays(rand(5, 30))->toDateString(),
                        ],
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                ]);

                $this->command->line("   Health #{$post->id}: {$title}");
            } else {
                $this->command->line("   Skipped (exists): {$slug}");
            }

            // ── Attach categories (sync is idempotent) ────────────────
            // Every health post gets at least one health-specific category
            // plus the general "Community Spotlight" category.
            $categoryIds = ($i % 2 === 0)
                ? [$catTips->id, $catCommunity->id]      // even index → Tips + Community
                : [$catScience->id, $catCommunity->id];  // odd index  → Science + Community

            $post->categories()->syncWithoutDetaching($categoryIds);
        }

        // ══════════════════════════════════════════════════════════════
        // STEP 4 — Success Story posts
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('❤️   Seeding 10 Success Story posts...');

        $storyTitles = [
            'How a Stranger\'s Gift Saved My Daughter in Dhaka',
            'Against All Odds: A Thalassemia Survivor\'s Journey',
            'The Call at 2 AM That Changed My Life in Chittagong',
            'From Recipient to Donor: My Three-Year Journey',
            'A Mother\'s Prayer Answered by an Unknown Hero in Sylhet',
            'Road Accident, Rare Blood, and a City That Responded',
            'Paying It Forward: Why I Donate Every Four Months',
            'The Night Roktodut Connected Me with My Blood Match',
            'Surgery in Rajshahi: How 5 Donors Showed Up in 2 Hours',
            'My First Donation and the Letter That Made Me Cry',
        ];

        foreach ($storyTitles as $i => $title) {
            $slug = Str::slug($title) . '-story-' . ($i + 1);

            $post = Post::where('slug', $slug)->first();

            if (! $post) {
                $district  = $faker->randomElement($districts);
                $refTypes  = ['donation', 'blood_request', null];
                $refType   = $faker->randomElement($refTypes);
                $anonymize = $faker->randomElement(['public', 'initials', 'anonymous']);

                $post = Post::create([
                    'author_user_id' => $faker->randomElement($authorIds),
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $this->storyExcerpt($faker),
                    'body_raw'       => $this->rawMarkdown($faker),
                    'body_sanitized' => $this->sanitizedStoryHtml($faker, $title, $district, $anonymize),
                    'cover_image'    => $faker->randomElement($coverImages),
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

            // ── Attach categories ────────────────────────────────────
            // Every story post gets "Donor Success Stories" + "Community Spotlight".
            $categoryIds = ($i % 3 === 0)
                ? [$catStories->id, $catCommunity->id, $catTips->id]  // extra Tips for every 3rd story
                : [$catStories->id, $catCommunity->id];

            $post->categories()->syncWithoutDetaching($categoryIds);
        }

        // ══════════════════════════════════════════════════════════════
        // Summary
        // ══════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('✅ BlogSeeder complete:');
        $this->command->info('   ↳ 4 default categories upserted');
        $this->command->info('   ↳ 10 Health Blog posts + health_blog_meta + category links');
        $this->command->info('   ↳ 10 Success Story posts + success_story_meta + category links');
    }

    // ══════════════════════════════════════════════════════════════════
    // Private content generators
    // ══════════════════════════════════════════════════════════════════

    private function rawMarkdown(\Faker\Generator $faker): string
    {
        $parts = [];
        for ($i = 0; $i < 4; $i++) {
            $parts[] = '## ' . $faker->sentence(4) . "\n\n" . $faker->paragraph(5);
        }
        return implode("\n\n", $parts);
    }

    private function sanitizedHealthHtml(\Faker\Generator $faker, string $title): string
    {
        $safeTitle   = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $subheadings = [
            'What the Science Says',
            'Key Benefits for Regular Donors',
            'Who Is Eligible to Donate?',
            'Preparing for Your Donation Day',
            'What to Expect at the Donation Centre',
            'Long-Term Health Impact',
        ];

        $sections = '';
        for ($i = 0; $i < 4; $i++) {
            $h     = htmlspecialchars($faker->randomElement($subheadings), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $para1 = htmlspecialchars($faker->paragraph(4), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $para2 = htmlspecialchars($faker->paragraph(3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $li1   = htmlspecialchars($faker->sentence(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $li2   = htmlspecialchars($faker->sentence(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $li3   = htmlspecialchars($faker->sentence(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $sections .= "\n<h2>{$h}</h2>\n"
                       . "<p>{$para1}</p>\n"
                       . "<ul><li>{$li1}</li><li>{$li2}</li><li>{$li3}</li></ul>\n"
                       . "<p>{$para2}</p>";
        }

        return "<article>\n"
             . "<h1>{$safeTitle}</h1>"
             . $sections
             . "\n<p><strong>Always consult a certified medical professional before donating blood.</strong></p>"
             . "\n</article>";
    }

    private function sanitizedStoryHtml(
        \Faker\Generator $faker,
        string $title,
        string $district,
        string $anonymize
    ): string {
        $safeTitle    = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeDistrict = htmlspecialchars($district, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $narratorName = match ($anonymize) {
            'initials'  => 'F. R.',
            'anonymous' => 'An Anonymous Donor',
            default     => htmlspecialchars($faker->name(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        };

        $para1 = htmlspecialchars($faker->paragraph(5), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $para2 = htmlspecialchars($faker->paragraph(4), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $para3 = htmlspecialchars($faker->paragraph(4), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $para4 = htmlspecialchars($faker->paragraph(3), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return "<article>\n"
             . "<h1>{$safeTitle}</h1>\n"
             . "<p><em>Story shared from <strong>{$safeDistrict}</strong> by {$narratorName}.</em></p>\n"
             . "<h2>The Beginning</h2>\n<p>{$para1}</p>\n"
             . "<h2>A Desperate Search</h2>\n<p>{$para2}</p>\n"
             . "<h2>When Help Arrived</h2>\n<p>{$para3}</p>\n"
             . "<h2>Life After the Donation</h2>\n<p>{$para4}</p>\n"
             . "<p><strong>Thank you to every blood donor who gives the gift of life.</strong></p>\n"
             . "</article>";
    }

    private function healthExcerpt(\Faker\Generator $faker): string
    {
        $openers = [
            'Blood donation saves millions of lives every year.',
            'Understanding your blood type can literally be lifesaving.',
            'Regular donors experience measurable health benefits.',
            'The human body replenishes donated blood within 24 to 48 hours.',
        ];
        return $faker->randomElement($openers) . ' ' . $faker->sentence(12);
    }

    private function storyExcerpt(\Faker\Generator $faker): string
    {
        $openers = [
            'I never expected a stranger\'s generosity to save my family.',
            'The night everything changed began with a single phone call.',
            'Finding the right blood type felt impossible until Roktodut.',
            'I used to be afraid to donate. Now I do it every four months.',
        ];
        return $faker->randomElement($openers) . ' ' . $faker->sentence(10);
    }
}
