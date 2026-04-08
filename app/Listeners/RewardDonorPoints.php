<?php

namespace App\Listeners;

use App\Events\DonationCompleted;
use App\Services\GamificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * ShouldQueue ইমপ্লিমেন্ট করার কারণে Laravel এটি স্বয়ংক্রিয়ভাবে
 * ব্যাকগ্রাউন্ড Queue-তে পাঠাবে। মূল HTTP request ধীর হবে না।
 */
class RewardDonorPoints implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Queue-তে retry করার সর্বোচ্চ সংখ্যা (নেটওয়ার্ক সমস্যায়)
     */
    public int $tries = 3;

    /**
     * retry-র মধ্যে বিলম্ব (সেকেন্ডে)
     */
    public int $backoff = 10;

    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    /**
     * ইভেন্ট হ্যান্ডেল করা — GamificationService কল করা হচ্ছে।
     */
    public function handle(DonationCompleted $event): void
    {
        try {
            $this->gamification->processDonationReward(
                donor:            $event->donor,
                bloodRequest:     $event->bloodRequest,
                isFirstResponder: $event->isFirstResponder,
            );

            Log::info('✅ Gamification reward processed', [
                'donor_id'         => $event->donor->id,
                'blood_request_id' => $event->bloodRequest->id,
                'first_responder'  => $event->isFirstResponder,
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ RewardDonorPoints listener failed', [
                'donor_id' => $event->donor->id,
                'error'    => $e->getMessage(),
            ]);

            // Queue-কে জানানো হচ্ছে retry করার জন্য
            $this->fail($e);
        }
    }

    /**
     * সব retry ব্যর্থ হলে এই মেথড কল হবে।
     */
    public function failed(DonationCompleted $event, \Throwable $exception): void
    {
        Log::critical('🔥 RewardDonorPoints permanently failed after retries', [
            'donor_id' => $event->donor->id,
            'error'    => $exception->getMessage(),
        ]);
    }
}
