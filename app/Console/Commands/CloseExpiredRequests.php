<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BloodRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CloseExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     * এটি টার্মিনালে কল করার জন্য ব্যবহার হবে।
     */
    protected $signature = 'requests:close-expired';

    /**
     * The console command description.
     */
    protected $description = 'Automatically close blood requests that are 24 hours past their needed time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired requests cleanup...');

        // বর্তমান সময় থেকে ২৪ ঘণ্টা আগের সময় বের করা
        $thresholdTime = Carbon::now()->subHours(24);

        // ডাটাবেস আপডেট কোয়েরি
        $expiredCount = BloodRequest::whereNotIn('status', ['fulfilled', 'closed'])
                                    ->where('needed_at', '<', $thresholdTime)
                                    ->update(['status' => 'closed']);

        // অ্যাডমিনদের ট্র্যাকিংয়ের জন্য লগ (Log) রাখা
        if ($expiredCount > 0) {
            Log::info("Auto-cleanup: {$expiredCount} expired blood requests have been closed.");
            $this->info("Success: {$expiredCount} requests closed.");
        } else {
            $this->info('No expired requests found.');
        }

        return Command::SUCCESS;
    }
}