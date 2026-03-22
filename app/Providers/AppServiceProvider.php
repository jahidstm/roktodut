<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🛡️ Anti-Scraping Privacy Shield for Donor Phone Numbers
        RateLimiter::for('phone-reveal', function (Request $request) {
    
            if ($request->user() && $request->user()->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinutes(15, 5)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request) {
                    // যদি রিকোয়েস্টটি AJAX/JSON হয়
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'অত্যধিক রিকোয়েস্ট! স্প্যাম প্রতিরোধের জন্য আপনাকে সাময়িকভাবে ব্লক করা হয়েছে। অনুগ্রহ করে ১৫ মিনিট পর আবার চেষ্টা করুন।'
                        ], 429); // 429 = Too Many Requests
                    }

                    // সাধারণ ব্রাউজার রিকোয়েস্টের জন্য রিডাইরেক্ট
                    return back()->with('error', 'অত্যধিক রিকোয়েস্ট! অনুগ্রহ করে ১৫ মিনিট পর আবার চেষ্টা করুন।');
                });
        });
    }
}