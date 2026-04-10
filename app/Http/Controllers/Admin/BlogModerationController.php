<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Post;
use App\Models\BloodRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * BlogModerationController — RoktoDut Admin Module
 *
 * Provides a moderation queue for user-submitted blog posts.
 * All routes protected via ['auth', 'role:admin'] middleware (see web.php).
 *
 * Actions:
 *  • index()   — fetch pending_review posts (eager-loaded author + meta)
 *  • approve() — publish a post; optionally mark is_verified_story = true
 *  • reject()  — reject a post with an optional reason
 *
 * Performance notes:
 *  • Eager-loads author and meta in a single query to avoid N+1
 *  • Uses the existing scopePendingReview() defined on the Post model
 *  • Database changes are wrapped in transactions for atomicity
 */
class BlogModerationController extends Controller
{
    // =========================================================================
    // ── Index — Moderation Queue ──────────────────────────────────────────────
    // =========================================================================

    /**
     * Display all posts with status = 'pending_review'.
     *
     * Eager loads:
     *  - author (User) — name, profile image
     *  - storyMeta     — anonymize_level, donation_ref_id, is_verified_story
     *  - healthMeta    — reviewer info (for type = health)
     *
     * Ordered by submission time (oldest first) so moderators work FIFO.
     *
     * @return View
     */
    public function index(): View
    {
        $pendingPosts = Post::with([
                'author',
                'storyMeta',
                'healthMeta',
                'categories',
            ])
            ->pendingReview()
            ->oldest('created_at')
            ->paginate(20);

        // Summary counts for the admin dashboard sidebar/header
        $stats = [
            'total_pending'  => Post::pendingReview()->count(),
            'total_stories'  => Post::pendingReview()->successStories()->count(),
            'total_health'   => Post::pendingReview()->healthBlogs()->count(),
            'total_rejected' => Post::where('status', 'rejected')->count(),
        ];

        return view('admin.blog.moderation.index', compact('pendingPosts', 'stats'));
    }

    // =========================================================================
    // ── Approve ───────────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Approve a pending post and publish it.
     *
     * Optional: if the post is a success story AND has a valid donation_ref_id,
     * the admin may mark is_verified_story = true by passing `verify_story=1`
     * in the request payload.
     *
     * Steps:
     *  1. Find the post (must be pending_review, abort 404 otherwise)
     *  2. Wrap DB changes in a transaction
     *  3. Update status → published, set published_at = now()
     *  4. If story + verify_story requested + valid ref → mark is_verified_story
     *
     * @param  Request  $request
     * @param  Post     $post      Route model binding
     * @return RedirectResponse
     */
    public function approve(Request $request, Post $post): RedirectResponse
    {
        // Only pending posts may be approved through this action
        if (!$post->isPendingReview()) {
            return back()->with('error', 'শুধুমাত্র পেন্ডিং পোস্ট অনুমোদন করা যাবে।');
        }

        DB::transaction(function () use ($request, $post) {

            // ── Publish the post ──────────────────────────────────────────
            $post->update([
                'status'       => 'published',
                'published_at' => now(),
            ]);

            // ── Story Verification (PRD §3.3) ─────────────────────────────
            if ($post->isSuccessStory() && $post->storyMeta) {
                $meta           = $post->storyMeta;
                $wantsVerify    = $request->boolean('verify_story');
                $hasRef         = filled($meta->donation_ref_id) && filled($meta->donation_ref_type);

                if ($wantsVerify && $hasRef && $this->refIsValid($meta)) {
                    $meta->update(['is_verified_story' => true]);
                }
            }
        });

        $label = $post->isSuccessStory()
            ? '✅ সাফল্যের গল্পটি প্রকাশিত হয়েছে'
            : '✅ স্বাস্থ্য ব্লগটি প্রকাশিত হয়েছে';

        if ($request->boolean('verify_story') && $post->storyMeta?->is_verified_story) {
            $label .= ' এবং "যাচাইকৃত গল্প" হিসেবে চিহ্নিত হয়েছে।';
        } else {
            $label .= '।';
        }

        return redirect()
            ->route('admin.blog.moderation.index')
            ->with('success', $label);
    }

    // =========================================================================
    // ── Reject ────────────────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Reject a pending post.
     *
     * Optionally accepts a `rejection_reason` string to store for transparency
     * (future: send notification to author).
     *
     * @param  Request  $request
     * @param  Post     $post      Route model binding
     * @return RedirectResponse
     */
    public function reject(Request $request, Post $post): RedirectResponse
    {
        if (!$post->isPendingReview()) {
            return back()->with('error', 'শুধুমাত্র পেন্ডিং পোস্ট বাতিল করা যাবে।');
        }

        // Optional rejection reason (max 500 chars) for admin notes / author notification
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $post) {
            $post->update(['status' => 'rejected']);

            // Future hook: store rejection reason + fire author notification
            // NotificationService::notifyRejection($post, $request->rejection_reason);
        });

        return redirect()
            ->route('admin.blog.moderation.index')
            ->with('error', "❌ পোস্টটি বাতিল করা হয়েছে: \"{$post->title}\"");
    }

    // =========================================================================
    // ── Private Helpers ───────────────────────────────────────────────────────
    // =========================================================================

    /**
     * Verify that the donation_ref_id in a story's meta actually exists
     * in the corresponding table and belongs to the post's author.
     *
     * This prevents admins from accidentally marking unrelated stories as
     * verified if the user provided a fabricated ID.
     *
     * @param  \App\Models\SuccessStoryMeta  $meta
     * @return bool
     */
    private function refIsValid(\App\Models\SuccessStoryMeta $meta): bool
    {
        $refId     = $meta->donation_ref_id;
        $refType   = $meta->donation_ref_type;
        $authorId  = $meta->post->author_user_id;

        return match ($refType) {
            'donation' => Donation::where('id', $refId)
                                  ->where('donor_id', $authorId)
                                  ->exists(),

            'blood_request' => BloodRequest::where('id', $refId)
                                           ->exists(),

            default => false,
        };
    }
}
