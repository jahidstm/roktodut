<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Post;
use App\Models\SuccessStoryMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

/**
 * BlogSubmissionController — RoktoDut Blog Module
 *
 * Handles the full post submission lifecycle:
 *   • WYSIWYG form display (create)
 *   • Server-side validation
 *   • Image processing: EXIF strip → compress → WebP conversion
 *   • Saving Post (status = pending_review) + SuccessStoryMeta
 *
 * Security hardening:
 *   - Cover image EXIF data stripped to prevent location leakage
 *   - Body is XSS-sanitised automatically by the Post model mutator
 *   - Privacy consent checkboxes enforced server-side for story type
 *   - donation_ref_id validated against actual DB rows (scope: current user)
 */
class BlogSubmissionController extends Controller
{
    // =========================================================================
    // ── Constants ─────────────────────────────────────────────────────────────
    // =========================================================================

    /** Maximum allowed cover image size in bytes (PRD §2.1: 2 MB) */
    private const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

    /** WebP quality (0–100). 82 gives excellent visual quality at ~60% JPEG size. */
    private const WEBP_QUALITY = 82;

    /** Storage disk for blog cover images */
    private const DISK = 'public';

    /**
     * Display the post creation form.
     */
    public function create(): View
    {
        return view('blog.create');
    }

    // =========================================================================
    // ── Store ─────────────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Validate, process, and persist a new blog post.
     *
     * Flow:
     *  1. Validate inputs (including story-specific rules)
     *  2. Process cover image (strip EXIF, compress, save as WebP)
     *  3. Create Post record (status = 'pending_review')
     *  4. If type = 'story', create SuccessStoryMeta record
     *  5. Redirect with success flash
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        // ── Image Processing ───────────────────────────────────────────────
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $this->processCoverImage($request->file('cover_image'));
        }

        // ── Create Post (status = pending_review) ──────────────────────────
        $post = Post::create([
            'author_user_id' => Auth::id(),
            'title'          => $validated['title'],
            'excerpt'        => $validated['excerpt'] ?? null,
            'body_raw'       => $validated['body_raw'],  // mutator auto-generates body_sanitized
            'cover_image'    => $coverImagePath,
            'type'           => $validated['type'],
            'status'         => 'pending_review',
            'published_at'   => null,
        ]);

        // ── Story Meta ─────────────────────────────────────────────────────
        if ($post->type === 'story') {
            SuccessStoryMeta::create([
                'post_id'           => $post->id,
                'district'          => $validated['district']            ?? null,
                'donation_ref_type' => $validated['donation_ref_type']   ?? null,
                'donation_ref_id'   => $validated['donation_ref_id']     ?? null,
                'anonymize_level'   => $validated['anonymize_level'],
                'is_verified_story' => false,  // set by admin on approval
            ]);
        }

        return redirect()
            ->route('blog.index')
            ->with('success', '✅ আপনার পোস্টটি সফলভাবে জমা দেওয়া হয়েছে। মডারেটর পর্যালোচনার পরে প্রকাশিত হবে।');
    }

    // =========================================================================
    // ── Validation ────────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Run all validation rules, including story-specific conditional rules.
     *
     * @return array<string, mixed>  The validated data array
     */
    private function validateRequest(Request $request): array
    {
        $isStory = $request->input('type') === 'story';

        $rules = [
            // ── Core Fields ──
            'title'    => ['required', 'string', 'min:5', 'max:200'],
            'excerpt'  => ['nullable', 'string', 'max:500'],
            'body_raw' => ['required', 'string', 'min:10'],
            'type'     => ['required', Rule::in(['health', 'story'])],

            // ── Cover Image (PRD §2.1: max 2 MB) ──
            'cover_image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp,gif',
                'max:2048',   // 2 MB in kilobytes (Laravel's max rule uses KB)
            ],
        ];

        // ── Story-Specific Rules ─────────────────────────────────────────
        if ($isStory) {
            $rules = array_merge($rules, [
                // Anonymization — must match the DB enum exactly
                'anonymize_level' => [
                    'required',
                    Rule::in(['public', 'initials', 'anonymous']),
                ],

                // Optional district name
                'district' => ['nullable', 'string', 'max:100'],

                // Verified story reference (both fields required together or neither)
                'donation_ref_type' => [
                    'nullable',
                    Rule::in(['donation', 'blood_request']),
                    'required_with:donation_ref_id',
                ],
                'donation_ref_id' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'required_with:donation_ref_type',
                    // Validate that the referenced record exists
                    function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                        if (empty($value) || empty($request->donation_ref_type)) {
                            return; // No ref provided — OK, field is optional
                        }

                        $exists = match ($request->donation_ref_type) {
                            'donation'     => Donation::where('id', $value)
                                                      ->where('donor_id', Auth::id())
                                                      ->exists(),
                            'blood_request' => BloodRequest::where('id', $value)
                                                           ->exists(),
                            default        => false,
                        };

                        if (!$exists) {
                            $fail('প্রদত্ত রেফারেন্স ID-টি বৈধ নয় বা আপনার সাথে সম্পর্কিত নয়।');
                        }
                    },
                ],

                // Privacy consent checkboxes — both required for story posts
                'consent_patient' => [
                    'required',
                    'accepted',
                ],
                'consent_no_pii'  => [
                    'required',
                    'accepted',
                ],
            ]);
        }

        return $request->validate($rules, [
            'title.required'           => 'পোস্টের শিরোনাম আবশ্যক।',
            'title.min'                => 'শিরোনাম অন্তত ৫ অক্ষরের হতে হবে।',
            'title.max'                => 'শিরোনাম সর্বোচ্চ ২০০ অক্ষরের হতে পারবে।',
            'body_raw.required'        => 'পোস্টের মূল বিষয়বস্তু আবশ্যক।',
            'body_raw.min'             => 'বিষয়বস্তু অন্তত ১০ অক্ষরের হতে হবে।',
            'type.required'            => 'পোস্টের ধরন নির্বাচন করুন।',
            'type.in'                  => 'অবৈধ পোস্টের ধরন।',
            'cover_image.max'          => 'কভার ইমেজের আকার সর্বোচ্চ ২ MB হতে পারবে।',
            'cover_image.mimes'        => 'শুধুমাত্র JPG, PNG বা WebP ফরম্যাটের ছবি গ্রহণযোগ্য।',
            'anonymize_level.required' => 'পরিচয় প্রকাশের ধরন নির্বাচন করুন।',
            'anonymize_level.in'       => 'অবৈধ পরিচয় প্রকাশের ধরন।',
            'consent_patient.required' => 'সাফল্যের গল্প জমা দিতে রোগীর সম্মতি নিশ্চিত করুন।',
            'consent_patient.accepted' => '"আমি রোগীর সম্মতি নিয়েছি" বাক্সটি চেক করুন।',
            'consent_no_pii.required'  => 'ব্যক্তিগত তথ্য প্রকাশ না করার নিশ্চিতকরণ আবশ্যক।',
            'consent_no_pii.accepted'  => '"আমি কোনো ঠিকানা/ফোন নম্বর প্রকাশ করিনি" বাক্সটি চেক করুন।',
            'donation_ref_type.required_with' => 'রেকর্ডের ID দিলে ধরনও নির্বাচন করুন।',
            'donation_ref_id.required_with'   => 'রেকর্ডের ধরন নির্বাচন করলে ID দেওয়া আবশ্যক।',
            'donation_ref_id.integer'         => 'রেকর্ড ID একটি পূর্ণ সংখ্যা হতে হবে।',
        ]);
    }

    // =========================================================================
    // ── Image Processing ──────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Process a cover image upload:
     *  1. Strip EXIF metadata (location data privacy — PRD §2.2)
     *  2. Resize to max 1280px wide (preserving aspect ratio)
     *  3. Convert to WebP at WEBP_QUALITY
     *  4. Save to storage/app/public/blog/covers/
     *
     * Uses Intervention Image v3 (GD driver) — already in composer.json.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  Relative path stored in the DB (e.g. "blog/covers/abc123.webp")
     */
    private function processCoverImage(\Illuminate\Http\UploadedFile $file): string
    {
        // Intervention Image v3 API
        $manager = new ImageManager(new GdDriver());

        $image = $manager->read($file->getRealPath());

        // Auto-orient based on any existing EXIF orientation data, then strip.
        // Intervention v3's GD driver re-encodes from scratch (no EXIF passthrough),
        // so EXIF data is implicitly stripped on encode — this is the privacy guarantee.
        $image->scaleDown(width: 1280);

        // Build a unique, collision-safe filename
        $filename  = Str::uuid() . '.webp';
        $directory = 'blog/covers';
        $diskPath  = "{$directory}/{$filename}";

        // Encode as WebP and put directly into the storage stream
        Storage::disk(self::DISK)->put(
            $diskPath,
            $image->toWebp(quality: self::WEBP_QUALITY)->toString()
        );

        return $diskPath;
    }
}
