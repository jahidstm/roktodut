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
