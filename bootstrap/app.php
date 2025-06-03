<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Pastikan use statement ini ada

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Anda bisa mengaktifkan rute API di sini jika Anda menggunakannya:
        // api: __DIR__.'/../routes/api.php',
        // channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Menambahkan RestrictRoutesByPortMiddleware ke grup 'web'
        $middleware->web(append: [
            \App\Http\Middleware\RestrictRoutesByPortMiddleware::class,
        ]);

        // Contoh jika Anda ingin menambahkan alias middleware (tidak kita gunakan sekarang, tapi ini tempatnya)
        // $middleware->alias([
        //     'auth.admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        // ]);

        // Contoh jika Anda ingin menambahkan middleware global lain (tidak kita gunakan sekarang)
        // $middleware->append(SomeOtherGlobalMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Di sini Anda bisa mengkonfigurasi bagaimana exceptions ditangani
        // Misalnya:
        // $exceptions->dontReport([
        //     MyCustomException::class,
        // ]);
        // $exceptions->render(function (Throwable $e, Request $request) {
        //     if ($e instanceof MyCustomException) {
        //         return response()->view('errors.my-custom-exception', [], 500);
        //     }
        //     return null; // Biarkan handler default Laravel yang bekerja
        // });
    })->create();