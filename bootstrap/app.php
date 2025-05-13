<?php

use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'userMiddleware' => UserMiddleware::class,
            'adminMiddleware' => AdminMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:konfirmasi-jadwal-otomatis')
            ->dailyAt('00:01')
            ->sendOutputTo(storage_path('logs/scheduler_konfirmasi.log'))
            ->appendOutputTo(storage_path('logs/scheduler_konfirmasi.log'));

        $schedule->command('app:insert-history-jadwal-pelayanan')
            ->monthlyOn(1, '00:01')
            ->sendOutputTo(storage_path('logs/scheduler_history.log'))
            ->appendOutputTo(storage_path('logs/scheduler_history.log'));
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
