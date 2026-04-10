<?php

namespace App\Console\Commands;

use App\Services\PostViewCacheService;
use Illuminate\Console\Command;

/**
 * Artisan Command: blog:flush-views
 *
 * Flushes pending Redis view-count deltas to the posts.view_count column
 * in MySQL via a single efficient batch UPDATE.
 *
 * Schedule recommendation (app/Console/Kernel.php or routes/console.php):
 *
 *   Schedule::command('blog:flush-views')->everyFiveMinutes();
 *
 * This keeps the DB write load low (one batch UPDATE every 5 min instead of
 * one UPDATE per page view), while keeping view counts reasonably fresh.
 */
class FlushPostViewsCommand extends Command
{
    protected $signature   = 'blog:flush-views';
    protected $description = 'Flush Redis post view-count counters to the database (batch UPDATE).';

    public function handle(PostViewCacheService $viewCache): int
    {
        $this->info('Flushing post view counts from Redis → MySQL…');

        $result = $viewCache->flushToDatabase();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Posts updated', $result['flushed']],
                ['Posts skipped (no delta)', $result['skipped']],
            ]
        );

        $this->info('Done.');

        return self::SUCCESS;
    }
}
