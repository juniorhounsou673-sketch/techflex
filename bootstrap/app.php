<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'active' => EnsureUserIsActive::class,
        ]);

        $middleware->appendToGroup('web', EnsureUserIsActive::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('techflex:send-top weekly')
            ->weeklyOn(1, '08:00')
            ->timezone('Africa/Porto-Novo')
            ->environments(['production'])
            ->withoutOverlapping(180)
            ->onOneServer();

        $schedule->command('techflex:send-top monthly')
            ->monthlyOn(1, '09:00')
            ->timezone('Africa/Porto-Novo')
            ->environments(['production'])
            ->withoutOverlapping(180)
            ->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
