<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories', 'storyMeta', 'healthMeta'])
            ->published()
            ->latest('published_at');

        // Apply type filter if requested
        if ($request->filled('type') && in_array($request->type, ['health', 'story'])) {
            $query->where('type', $request->type);
        }

        // Apply text search if requested
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10);

        // Fetch popular posts for the sidebar
        $popularPosts = Post::published()
            ->orderByDesc('view_count') // Ideally we'd use the attribute logic here, but for DB sorting we can use view_count
            ->limit(5)
            ->get();

        return view('blog.index', compact('posts', 'popularPosts'));
    }

    /**
     * Display the specified blog post.
     *
     * Admin Preview Bypass: Admins can view posts of any status (pending_review,
     * draft, etc.) to facilitate moderation. All other users are restricted to
     * published posts only — a mismatch results in a standard 404.
     */
    public function show(string $slug)
    {
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        $query = Post::with(['author', 'categories', 'storyMeta', 'healthMeta.reviewer'])
            ->where('slug', $slug);

        // Guests and regular donors may only see published posts.
        if (! $isAdmin) {
            $query->where('status', 'published');
        }

        $post = $query->firstOrFail();

        // Only count views for publicly-accessible, published posts.
        if ($post->isPublished()) {
            $post->incrementView();
        }

        // Fetch popular posts for the sidebar (always published)
        $popularPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        return view('blog.show', compact('post', 'popularPosts'));
    }
}
