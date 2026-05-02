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
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureViteDevServer();

        Event::listen(
            Login::class,
            LogSuccessfulLogin::class,
        );

        Event::listen(
            DonationCompleted::class,
            RewardDonorPoints::class,
        );

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('পাসওয়ার্ড রিসেট নোটিফিকেশন')
                ->greeting('হ্যালো!')
                ->line('আপনি এই ইমেইলটি পেয়েছেন কারণ আমরা আপনার অ্যাকাউন্টের জন্য একটি পাসওয়ার্ড রিসেট অনুরোধ পেয়েছি।')
                ->action('পাসওয়ার্ড রিসেট করুন', $url)
                ->line('আপনার পাসওয়ার্ড রিসেট লিংকটি ৬০ মিনিটের মধ্যে অকার্যকর হয়ে যাবে।')
                ->line('আপনি যদি পাসওয়ার্ড রিসেটের অনুরোধ না করে থাকেন, তবে আর কোনো পদক্ষেপের প্রয়োজন নেই।');
        });

        // ─── Contact Form Throttle ────────────────────────────────────────────
        // গেস্ট: ১ মিনিটে সর্বোচ্চ ২ রিকোয়েস্ট (IP ভিত্তিক)
        RateLimiter::for('contact-guest', function (Request $request) {
            return Limit::perMinute(2)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'অত্যধিক বার্তা পাঠানো হচ্ছে। অনুগ্রহ করে ১ মিনিট পরে আবার চেষ্টা করুন।');
                });
        });

        // লগইন ইউজার: ১ মিনিটে সর্বোচ্চ ৫ রিকোয়েস্ট (ইউজার ID ভিত্তিক)
        RateLimiter::for('contact-auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?? $request->ip())
                ->response(function () {
                    return back()->with('error', 'অত্যধিক বার্তা পাঠানো হচ্ছে। অনুগ্রহ করে ১ মিনিট পরে আবার চেষ্টা করুন।');
                });
        });
        // ────────────────────────────────────────────────────────────────────

        RateLimiter::for('requests-store', function (Request $request) {
            $message = 'অনেক বেশি অনুরোধ করা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।';

            return ($request->user() ? Limit::perMinute(10) : Limit::perMinute(5))
                ->by('requests-store:' . $request->ip())
                ->response(function (Request $request) use ($message) {
                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 429);
                    }

                    return back()->withInput()->with('error', $message);
                });
        });

        RateLimiter::for('reports-submit', function (Request $request) {
            $message = 'অল্প সময়ের মধ্যে অনেক বেশি রিপোর্ট করা হয়েছে। অনুগ্রহ করে ১ মিনিট পরে আবার চেষ্টা করুন।';

            return Limit::perMinute(3)
                ->by('reports-submit:' . $request->ip())
                ->response(function (Request $request) use ($message) {
                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 429);
                    }

                    return back()->withInput()->with('error', $message);
                });
        });

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

    private function configureViteDevServer(): void
    {
        if (! $this->app->environment('local')) {
            return;
        }

        if (config('app.vite_dev_server')) {
            return;
        }

        // Force built assets even if a stale public/hot file exists.
        Vite::useHotFile(storage_path('framework/vite.hot'));
    }
}
