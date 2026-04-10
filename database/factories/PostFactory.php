<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    // Safe health/lifestyle image placeholders (Picsum, no auth required)
    private const COVER_IMAGES = [
        'https://picsum.photos/seed/blood1/1200/630',
        'https://picsum.photos/seed/health2/1200/630',
        'https://picsum.photos/seed/donor3/1200/630',
        'https://picsum.photos/seed/story4/1200/630',
        'https://picsum.photos/seed/life5/1200/630',
        'https://picsum.photos/seed/care6/1200/630',
    ];

    // Bangladeshi districts for story meta
    private const DISTRICTS = [
        'Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi',
        'Khulna', 'Barisal', 'Rangpur', 'Mymensingh',
        'Comilla', 'Gazipur', 'Narayanganj', 'Bogura',
    ];

    // ── Base definition (override via states) ─────────────────────────

    public function definition(): array
    {
        $title     = $this->faker->sentence(6);
        $slug      = Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999);
        $publishedAt = $this->faker->dateTimeBetween('-6 months', '-1 day');

        return [
            'author_user_id' => User::factory(),
            'title'          => $title,
            'slug'           => $slug,
            'excerpt'        => $this->faker->paragraph(2),
            'body_raw'       => $this->rawMarkdownBody(),
            'body_sanitized' => $this->sanitizedHtmlBody(),
            'cover_image'    => $this->faker->randomElement(self::COVER_IMAGES),
            'type'           => $this->faker->randomElement(['health', 'story']),
            'status'         => 'draft',
            'published_at'   => null,
        ];
    }

    // ── States ─────────────────────────────────────────────────────────

    /** Mark post as published with a past published_at timestamp. */
    public function published(): static
    {
        return $this->state(fn(array $attrs) => [
            'status'       => 'published',
            'published_at' => $this->faker->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }

    public function healthBlog(): static
    {
        return $this->state(fn(array $attrs) => [
            'type'    => 'health',
            'title'   => $this->healthTitle(),
            'excerpt' => $this->faker->paragraph(2),
        ]);
    }

    public function successStory(): static
    {
        return $this->state(fn(array $attrs) => [
            'type'    => 'story',
            'title'   => $this->storyTitle(),
            'excerpt' => $this->faker->paragraph(2),
        ]);
    }

    // ── Body Generators ────────────────────────────────────────────────

    /**
     * Simulated raw Markdown / unsanitised source (what an author submits).
     */
    private function rawMarkdownBody(): string
    {
        $paras = [];
        for ($i = 0; $i < 4; $i++) {
            $paras[] = '## ' . $this->faker->sentence(4) . "\n\n" . $this->faker->paragraph(5);
        }
        return implode("\n\n", $paras);
    }

    /**
     * Safe sanitised HTML — only uses a whitelist of tags a blog body would need.
     * In production this would be produced by HTMLPurifier / CommonMark.
     */
    private function sanitizedHtmlBody(): string
    {
        $sections = [];
        $headings = [
            'Understanding Blood Donation',
            'Why Your Blood Type Matters',
            'The Journey of a Lifesaving Gift',
            'Recovery & Aftercare Tips',
            'How to Register as a Donor',
        ];

        for ($i = 0; $i < 4; $i++) {
            $heading = $this->faker->randomElement($headings);
            $para1   = htmlspecialchars($this->faker->paragraph(4), ENT_QUOTES, 'UTF-8');
            $para2   = htmlspecialchars($this->faker->paragraph(3), ENT_QUOTES, 'UTF-8');

            $sections[] = "<h2>{$heading}</h2>"
                        . "<p>{$para1}</p>"
                        . "<ul>"
                        .   "<li>" . htmlspecialchars($this->faker->sentence(), ENT_QUOTES, 'UTF-8') . "</li>"
                        .   "<li>" . htmlspecialchars($this->faker->sentence(), ENT_QUOTES, 'UTF-8') . "</li>"
                        .   "<li>" . htmlspecialchars($this->faker->sentence(), ENT_QUOTES, 'UTF-8') . "</li>"
                        . "</ul>"
                        . "<p>{$para2}</p>";
        }

        return "<article>\n" . implode("\n", $sections) . "\n</article>";
    }

    // ── Title Generators ───────────────────────────────────────────────

    private function healthTitle(): string
    {
        $templates = [
            'Why %s Blood Donors Are Bangladesh\'s Silent Heroes',
            '%d Health Benefits of Donating Blood Regularly',
            'The Science Behind %s Blood Types Explained',
            'How Blood Donation Improves Your %s Health',
            'Everything You Need to Know About %s Compatibility',
        ];

        $tpl = $this->faker->randomElement($templates);

        return sprintf(
            $tpl,
            $this->faker->randomElement(['O+', 'A+', 'B+', 'AB+', 'O-', 'regular', 'voluntary']),
        );
    }

    private function storyTitle(): string
    {
        $templates = [
            'How a Stranger\'s Gift Saved My Life in %s',
            'My Journey from Recipient to Donor in %s',
            'The Call That Changed Everything: A %s Donor Story',
            'Against All Odds: A Survival Story from %s',
            'Paying It Forward: Blood Donation in %s',
        ];

        return sprintf(
            $this->faker->randomElement($templates),
            $this->faker->randomElement(self::DISTRICTS)
        );
    }

    // ── Meta Helpers (called from BlogSeeder) ─────────────────────────

    /**
     * Build a health_blog_meta data array for a given post.
     */
    public static function makeHealthMeta(\Faker\Generator $faker): array
    {
        $reviewers = [
            'Dr. Arif Hossain, MBBS, FCPS',
            'Dr. Nasrin Sultana, MD (Hematology)',
            'Dr. Kamal Uddin, MBBS',
            null, // some articles not yet reviewed
        ];

        return [
            'medically_reviewed_by' => $faker->randomElement($reviewers),
            'sources_json'          => json_encode([
                [
                    'title'       => 'WHO Blood Safety Guidelines 2024',
                    'url'         => 'https://www.who.int/publications/blood-safety',
                    'accessed_at' => $faker->date(),
                ],
                [
                    'title'       => $faker->sentence(5),
                    'url'         => 'https://pubmed.ncbi.nlm.nih.gov/' . $faker->numberBetween(10000000, 39999999),
                    'accessed_at' => $faker->date(),
                ],
            ]),
        ];
    }

    /**
     * Build a success_story_meta data array for a given post.
     */
    public static function makeStoryMeta(\Faker\Generator $faker): array
    {
        $refTypes = ['donation', 'blood_request', null];
        $refType  = $faker->randomElement($refTypes);

        return [
            'district'          => $faker->randomElement(self::DISTRICTS),
            'donation_ref_type' => $refType,
            'donation_ref_id'   => $refType ? $faker->numberBetween(1, 50) : null,
            'is_verified_story' => $faker->boolean(60), // 60 % verified
            'anonymize_level'   => $faker->randomElement(['public', 'initials', 'anonymous']),
        ];
    }
}
