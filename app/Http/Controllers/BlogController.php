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
     */
    public function show(string $slug)
    {
        $post = Post::with(['author', 'categories', 'storyMeta', 'healthMeta.reviewer'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Prevent viewing drafts unless authorized (you might want to add auth checks here later)
        if (!$post->isPublished()) {
            abort(404);
        }

        // Increment view count using the Redis helper
        $post->incrementView();

        // Fetch popular posts for the sidebar
        $popularPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        return view('blog.show', compact('post', 'popularPosts'));
    }
}
