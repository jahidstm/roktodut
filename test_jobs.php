<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$jobs = Illuminate\Support\Facades\DB::table('jobs')->get();
if ($jobs->isEmpty()) {
    dump('No jobs found in the queue.');
} else {
    foreach ($jobs as $job) {
        $payload = json_decode($job->payload);
        $displayName = $payload->displayName ?? 'Unknown';
        $createdAt = \Carbon\Carbon::createFromTimestamp($job->created_at)->toDateTimeString();
        $availableAt = \Carbon\Carbon::createFromTimestamp($job->available_at)->toDateTimeString();
        $diff = \Carbon\Carbon::createFromTimestamp($job->created_at)->diffInMinutes(\Carbon\Carbon::createFromTimestamp($job->available_at));
        dump(sprintf('Job: %s | Created: %s | Execution Time: %s | Delay: %d minutes', $displayName, $createdAt, $availableAt, $diff));
    }
}
