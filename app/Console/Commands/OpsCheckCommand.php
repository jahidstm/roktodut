<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class OpsCheckCommand extends Command
{
    protected $signature = 'ops:check';
    protected $description = 'Production ops readiness check (queue/scheduler/reverb/storage/security)';

    public function handle(): int
    {
        $checks = [];

        $this->checkScheduler($checks);
        $this->checkQueue($checks);
        $this->checkBroadcastRealtime($checks);
        $this->checkStorage($checks);
        $this->checkSecurityFlags($checks);

        $this->newLine();
        $this->table(['Check', 'Status', 'Details', 'Action'], $checks);
        $this->newLine();

        $hasFail = collect($checks)->contains(fn(array $row) => $row[1] === 'FAIL');
        $hasWarn = collect($checks)->contains(fn(array $row) => $row[1] === 'WARN');

        if ($hasFail) {
            $this->error('Ops readiness check failed.');
            return self::FAILURE;
        }

        if ($hasWarn) {
            $this->warn('Ops readiness check passed with warnings.');
            return self::SUCCESS;
        }

        $this->info('Ops readiness check passed.');
        return self::SUCCESS;
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkScheduler(array &$checks): void
    {
        $events = app(Schedule::class)->events();
        if (count($events) === 0) {
            $checks[] = [
                'Scheduler events',
                'FAIL',
                'No scheduled tasks found.',
                'Define tasks in routes/console.php and run cron/scheduler.',
            ];
            return;
        }

        $checks[] = [
            'Scheduler events',
            'PASS',
            count($events) . ' scheduled task(s) found.',
            'Ensure OS cron runs `php artisan schedule:run` every minute.',
        ];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkQueue(array &$checks): void
    {
        $connection = (string) config('queue.default', 'sync');

        if ($connection === 'sync') {
            $checks[] = [
                'Queue driver',
                'WARN',
                'QUEUE_CONNECTION=sync (no background worker).',
                'Use database/redis and run queue worker in production.',
            ];
            return;
        }

        if ($connection === 'database') {
            $table = (string) config('queue.connections.database.table', 'jobs');
            if (!Schema::hasTable($table)) {
                $checks[] = [
                    'Queue jobs table',
                    'FAIL',
                    "Missing `{$table}` table.",
                    'Run `php artisan queue:table` + migrate.',
                ];
                return;
            }
        }

        $checks[] = [
            'Queue driver',
            'PASS',
            "QUEUE_CONNECTION={$connection}",
            'Run worker (Supervisor/systemd): `php artisan queue:work --tries=3`.',
        ];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkBroadcastRealtime(array &$checks): void
    {
        $broadcast = (string) config('broadcasting.default', 'null');
        if ($broadcast !== 'reverb') {
            $checks[] = [
                'Realtime broadcast',
                'WARN',
                "BROADCAST_CONNECTION={$broadcast}",
                'Set reverb if realtime notifications are required.',
            ];
            return;
        }

        $required = [
            'REVERB_APP_ID' => env('REVERB_APP_ID'),
            'REVERB_APP_KEY' => env('REVERB_APP_KEY'),
            'REVERB_APP_SECRET' => env('REVERB_APP_SECRET'),
            'REVERB_HOST' => env('REVERB_HOST'),
            'REVERB_PORT' => env('REVERB_PORT'),
            'REVERB_SCHEME' => env('REVERB_SCHEME'),
        ];

        $missing = collect($required)
            ->filter(fn($v) => $v === null || $v === '')
            ->keys()
            ->values()
            ->all();

        if ($missing !== []) {
            $checks[] = [
                'Reverb env vars',
                'FAIL',
                'Missing: ' . implode(', ', $missing),
                'Set all REVERB_* vars and restart workers.',
            ];
            return;
        }

        $checks[] = [
            'Reverb env vars',
            'PASS',
            'All REVERB_* vars configured.',
            'Ensure `php artisan reverb:start` is supervised.',
        ];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkStorage(array &$checks): void
    {
        $publicStoragePath = public_path('storage');
        if (!File::exists($publicStoragePath)) {
            $checks[] = [
                'Public storage link',
                'FAIL',
                'public/storage is missing.',
                'Run `php artisan storage:link`.',
            ];
        } else {
            $checks[] = [
                'Public storage link',
                'PASS',
                'public/storage exists.',
                'Keep shared storage mounted in deployment.',
            ];
        }

        $privatePath = storage_path('app/private');
        if (!File::isDirectory($privatePath)) {
            $checks[] = [
                'Private storage path',
                'WARN',
                'storage/app/private directory missing.',
                'Create private storage directory with proper permissions.',
            ];
            return;
        }

        if (!is_writable($privatePath)) {
            $checks[] = [
                'Private storage writable',
                'FAIL',
                'storage/app/private is not writable.',
                'Fix ownership/permissions for web and queue users.',
            ];
            return;
        }

        $checks[] = [
            'Private storage writable',
            'PASS',
            'storage/app/private is writable.',
            'NID/proof uploads can be persisted.',
        ];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkSecurityFlags(array &$checks): void
    {
        $appUrl = (string) config('app.url', '');
        $sessionSecure = (bool) config('session.secure', false);
        $telegramToken = (string) config('services.telegram.token', '');
        $telegramSecret = (string) config('services.telegram.webhook_secret', '');

        if (str_starts_with($appUrl, 'https://')) {
            $checks[] = ['APP_URL scheme', 'PASS', "APP_URL={$appUrl}", 'Keep TLS certificate auto-renew enabled.'];
        } else {
            $checks[] = ['APP_URL scheme', 'WARN', "APP_URL={$appUrl}", 'Use HTTPS URL in production.'];
        }

        if ($sessionSecure) {
            $checks[] = ['Session secure cookie', 'PASS', 'SESSION_SECURE_COOKIE enabled.', 'Keep true in production.'];
        } else {
            $checks[] = ['Session secure cookie', 'WARN', 'SESSION_SECURE_COOKIE disabled.', 'Enable for HTTPS production deployments.'];
        }

        if ($telegramToken !== '' && $telegramSecret === '') {
            $checks[] = ['Telegram webhook secret', 'FAIL', 'TELEGRAM_BOT_TOKEN is set but TELEGRAM_WEBHOOK_SECRET is empty.', 'Set TELEGRAM_WEBHOOK_SECRET and re-run telegram:set-webhook.'];
            return;
        }

        $checks[] = ['Telegram webhook secret', 'PASS', 'Telegram secret configuration is consistent.', 'Re-run telegram:set-webhook when rotating secret.'];
    }
}
