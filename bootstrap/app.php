<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Komentari atau hapus pendaftaran VerySimpleTestMiddleware jika sudah selesai tes:
        // $middleware->prepend(\App\Http\Middleware\VerySimpleTestMiddleware::class);

        // Daftarkan RestrictRoutesByPortMiddleware secara global dengan prepend:
        $middleware->prepend(\App\Http\Middleware\RestrictRoutesByPortMiddleware::class);

        // Anda bisa membiarkan $middleware->web(...) kosong atau menghapusnya
        // jika tidak ada middleware lain yang secara spesifik perlu di-append atau di-prepend ke grup 'web' saat ini.
        // Misalnya, jika tidak ada middleware lain untuk grup web:
        // $middleware->web(append: [
        //     // middleware lain untuk web jika ada
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ... (konfigurasi exception Anda)
    })->create();