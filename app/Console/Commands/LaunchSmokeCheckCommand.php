<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class LaunchSmokeCheckCommand extends Command
{
    protected $signature = 'launch:smoke';
    protected $description = 'Run launch readiness smoke checks for critical routes/tables/config';

    public function handle(): int
    {
        $checks = [];

        $this->checkCriticalRoutes($checks);
        $this->checkCriticalTables($checks);
        $this->checkCriticalConfig($checks);

        $this->newLine();
        $this->table(['Check', 'Status', 'Details'], $checks);
        $this->newLine();

        $hasFail = collect($checks)->contains(fn(array $row) => $row[1] === 'FAIL');
        if ($hasFail) {
            $this->error('Launch smoke checks failed.');
            return self::FAILURE;
        }

        $hasWarn = collect($checks)->contains(fn(array $row) => $row[1] === 'WARN');
        if ($hasWarn) {
            $this->warn('Launch smoke checks passed with warnings.');
            return self::SUCCESS;
        }

        $this->info('Launch smoke checks passed.');
        return self::SUCCESS;
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkCriticalRoutes(array &$checks): void
    {
        $requiredRoutes = [
            'home',
            'requests.store',
            'search',
            'requests.donors.reveal_phone',
            'donor.upload_nid',
            'donor.view_nid',
            'telegram.webhook',
            'firebase.messaging.sw',
            'public.requests.index',
        ];

        $missing = collect($requiredRoutes)->filter(fn(string $name) => !Route::has($name))->values()->all();

        if ($missing !== []) {
            $checks[] = ['Critical routes', 'FAIL', 'Missing: ' . implode(', ', $missing)];
            return;
        }

        $checks[] = ['Critical routes', 'PASS', count($requiredRoutes) . ' routes found'];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkCriticalTables(array &$checks): void
    {
        $tables = [
            'users',
            'blood_requests',
            'blood_request_responses',
            'phone_reveal_logs',
            'audit_logs',
            'notifications',
            'divisions',
            'districts',
            'upazilas',
        ];

        $missing = collect($tables)->filter(fn(string $table) => !Schema::hasTable($table))->values()->all();

        if ($missing !== []) {
            $checks[] = ['Critical tables', 'FAIL', 'Missing: ' . implode(', ', $missing)];
            return;
        }

        $checks[] = ['Critical tables', 'PASS', count($tables) . ' tables available'];
    }

    /**
     * @param array<int, array<int, string>> $checks
     */
    private function checkCriticalConfig(array &$checks): void
    {
        $retentionDays = max(30, (int) config('privacy.nid_retention_days', 365));
        $checks[] = ['NID retention policy', 'PASS', "Configured: {$retentionDays} days"];

        $telegramToken = (string) config('services.telegram.token', '');
        $telegramSecret = (string) config('services.telegram.webhook_secret', '');

        if ($telegramToken !== '' && $telegramSecret === '') {
            $status = app()->environment('production') ? 'FAIL' : 'WARN';
            $checks[] = ['Telegram webhook secret', $status, 'Token is set but secret is empty'];
            return;
        }

        $checks[] = ['Telegram webhook secret', 'PASS', 'Secret configuration is consistent'];
    }
}
