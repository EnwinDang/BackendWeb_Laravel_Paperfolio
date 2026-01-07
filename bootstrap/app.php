<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Update asset prices every 5 minutes automatically
        // This balances accuracy with API rate limits (CoinGecko free tier: 10-50 calls/minute)
        // Note: Run 'php artisan scheduler:run' to start the scheduler manually
        $schedule->command('assets:update-prices')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
