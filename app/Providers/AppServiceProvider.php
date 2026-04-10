<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Division;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use App\Events\DonationCompleted;
use App\Listeners\RewardDonorPoints;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(
            Login::class,
            LogSuccessfulLogin::class,
        );

        Event::listen(
            DonationCompleted::class,
            RewardDonorPoints::class,
        );

        RateLimiter::for('phone-reveal', function (Request $request) {
            if ($request->user() && $request->user()->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinutes(15, 5)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'অত্যধিক রিকোয়েস্ট! স্প্যাম প্রতিরোধের জন্য আপনাকে সাময়িকভাবে ব্লক করা হয়েছে। অনুগ্রহ করে ১৫ মিনিট পর আবার চেষ্টা করুন।'
                        ], 429);
                    }

                    return back()->with('error', 'অত্যধিক রিকোয়েস্ট! অনুগ্রহ করে ১৫ মিনিট পর আবার চেষ্টা করুন।');
                });
        });

        View::composer('admin.layouts.sidebar', function ($view) {
            $view->with('pendingBlogCount', Post::pendingReview()->count());
        });
    }
}