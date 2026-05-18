<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'internal.secret' => \App\Http\Middleware\VerifyInternalSecret::class,
        ]);

        $middleware->trustProxies(at: '*');

        // 🤖 Telegram webhook CSRF থেকে বাদ রাখা (Telegram server থেকে POST আসে)
        $middleware->validateCsrfTokens(except: [
            'telegram/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            if (app()->environment('production') || app()->environment('local')) {
                // Ignore 404, 403 etc.
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && $e->getStatusCode() < 500) {
                    return;
                }

                try {
                    $telegram = app(\App\Services\TelegramService::class);
                    $message = "🚨 <b>System Error!</b>\n\n"
                        . "<b>Message:</b> " . \Illuminate\Support\Str::limit($e->getMessage(), 100) . "\n"
                        . "<b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "\n"
                        . "<b>URL:</b> " . request()->fullUrl() . "\n"
                        . "<b>User ID:</b> " . (auth()->id() ?? 'Guest');

                    $telegram->sendAdminAlert($message);
                } catch (\Throwable $err) {
                    // Fail silently to prevent loops
                }
            }
        });
    })->create();
