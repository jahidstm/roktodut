<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CooldownReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SendCooldownReminders
 *
 * State Machine Stages:
 *   0 → unsent   (never reminded)
 *   1 → 7-day reminder sent
 *   2 → 3-day reminder sent
 *   3 → day-of reminder sent (cycle complete)
 *
 * Runs daily. Each day it checks which donors are T-7, T-3, or T-0
 * days from their cooldown_until, and advances their reminder_stage
 * to prevent duplicate sends.
 */
class SendCooldownReminders extends Command
{
    protected $signature = 'reminders:send-cooldown
                            {--dry-run : Preview how many would be notified without actually sending}';

    protected $description = 'Send cooldown reminder notifications to donors (7-day, 3-day, day-of)';

    // State machine: days_left → required stage (sent AFTER this stage)
    private const STAGES = [
        7 => ['required_stage' => 0, 'next_stage' => 1],
        3 => ['required_stage' => 1, 'next_stage' => 2],
        0 => ['required_stage' => 2, 'next_stage' => 3],
    ];

    private bool $isDryRun = false;
    private int  $totalSent = 0;

    // ─────────────────────────────────────────────────────────────────────────
    public function handle(): int
    {
        $this->isDryRun = (bool) $this->option('dry-run');

        if ($this->isDryRun) {
            $this->warn('🔍 DRY RUN MODE — কোনো notification পাঠানো হবে না।');
        }

        $this->info('🔔 Cooldown Reminder job শুরু হয়েছে — ' . now()->format('Y-m-d H:i:s'));

        foreach (self::STAGES as $daysLeft => $stage) {
            $this->processStage($daysLeft, $stage['required_stage'], $stage['next_stage']);
        }

        $verb = $this->isDryRun ? 'পাঠানো হতো' : 'পাঠানো হয়েছে';
        $this->info("✅ সম্পন্ন। মোট {$this->totalSent} জন donor-কে reminder {$verb}।");

        Log::info('[CooldownReminder] Job completed.', [
            'dry_run'    => $this->isDryRun,
            'total_sent' => $this->totalSent,
            'date'       => now()->toDateString(),
        ]);

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core: একটি stage process করা
    // ─────────────────────────────────────────────────────────────────────────
    private function processStage(int $daysLeft, int $requiredStage, int $nextStage): void
    {
        $targetDate = Carbon::today()->addDays($daysLeft)->toDateString();

        $this->line("📅 T-{$daysLeft} reminder: cooldown_until = {$targetDate}, stage < {$nextStage}");

        // Query: donor যাদের cooldown_until ঠিক এই তারিখে এবং stage এখনো পাঠানো হয়নি
        User::query()
            ->where('is_donor', true)
            ->whereNotNull('cooldown_until')
            ->whereDate('cooldown_until', $targetDate)
            ->where('reminder_stage', '=', $requiredStage)  // State machine check
            ->select(['id', 'name', 'cooldown_until', 'fcm_token', 'telegram_chat_id', 'reminder_stage'])
            ->chunk(200, function ($donors) use ($daysLeft, $nextStage, $targetDate) {
                $count  = $donors->count();
                $label  = $daysLeft === 0 ? 'আজ' : "T-{$daysLeft}";

                if ($this->isDryRun) {
                    $this->line("  → [Dry Run] {$label}: {$count} জনকে পাঠানো হতো।");
                    $this->totalSent += $count;
                    return;
                }

                $eligibleDate = Carbon::parse($targetDate)->locale('bn')->isoFormat('D MMMM, YYYY');
                $notification = new CooldownReminderNotification($daysLeft, $eligibleDate);

                $userIds = $donors->pluck('id')->all();

                foreach ($donors as $donor) {
                    // 1. In-app (database) notification
                    $donor->notify($notification);

                    // 2. FCM push
                    $notification->sendFcm($donor);

                    // 3. Telegram
                    $notification->sendTelegram($donor);
                }

                // Batch update reminder_stage (single query — N+1 free)
                User::whereIn('id', $userIds)
                    ->update(['reminder_stage' => $nextStage]);

                $this->totalSent += $count;

                $this->info("  ✓ {$label}: {$count} জন donor-কে stage {$nextStage} তে আপডেট করা হয়েছে।");

                Log::info("[CooldownReminder] Stage {$nextStage} sent.", [
                    'days_left'  => $daysLeft,
                    'target_date' => $targetDate,
                    'count'      => $count,
                    'user_ids'   => array_slice($userIds, 0, 10), // log first 10 only
                ]);
            });
    }
}
