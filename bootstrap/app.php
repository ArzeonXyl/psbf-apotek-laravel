<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Hapus atau komentari prepend RestrictRoutesByPortMiddleware.
        // $middleware->prepend(\App\Http\Middleware\RestrictRoutesByPortMiddleware::class);

        // Tambahkan RestrictRoutesByPortMiddleware ke group 'web'.
        // Ini akan menempatkannya setelah middleware standar seperti StartSession, AuthenticateSession, dll.
        $middleware->web(append: [
            \App\Http\Middleware\RestrictRoutesByPortMiddleware::class,
            // Jika Anda memiliki RoleMiddleware yang terdaftar sebagai route middleware,
            // Anda TIDAK perlu menambahkannya di sini kecuali Anda ingin itu berjalan secara global
            // untuk setiap rute web yang tidak memiliki middleware 'auth' atau 'role'.
            // Namun, umumnya RoleMiddleware ditambahkan ke route spesifik (misal: Route::middleware('role:admin')).
            // Jika RoleMiddleware Anda juga perlu Auth::check() dan Auth::user()->role,
            // dan Anda mendaftarkannya secara global, itu juga harus di-append.
            // Contoh: \App\Http\Middleware\RoleMiddleware::class, // HANYA JIKA ANDA INGIN GLOBAL
        ]);

        // Opsional: Jika Anda ingin mendaftarkan route middleware baru
        // atau alias yang digunakan di Route::middleware('nama_alias'),
        // Anda bisa melakukannya di sini:
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            // Tambahkan alias middleware lainnya jika ada
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ... (konfigurasi exception Anda)
    })->create();