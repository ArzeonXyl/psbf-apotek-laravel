<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RestrictRoutesByPortMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $port = $request->getPort();
        $path = $request->path(); // Dapatkan path aktual, misal 'admin/login' atau 'admin'
        Log::info("RestrictMiddleware ENTRY: Port={$port}, Path='{$path}'");

        // Izinkan semua permintaan Livewire terlepas dari port, untuk kesederhanaan awal.
        if ($request->is('livewire/*')) {
            Log::info("RestrictMiddleware: Allowing livewire/* path for '{$path}'.");
            return $next($request);
        }

        // Logika untuk Port Apoteker/Toko (8001)
        if ($port == 8001) {
            Log::info("RestrictMiddleware: Processing Apoteker Port (8001) rules for path '{$path}'.");

            // Daftar path yang SECARA EKSPLISIT DILARANG di port 8001
            $forbiddenPathsOnTokoPort = [
                'admin',        // Untuk /admin
                'admin/*',      // Untuk semua di bawah /admin
                'login',
                'register',
                'forgot-password',
                'reset-password',
                'email/verification-notification',
                'verify-email',
                'confirm-password',
                'dashboard'     // Dashboard Breeze
            ];

            foreach ($forbiddenPathsOnTokoPort as $forbiddenPath) {
                if ($request->is($forbiddenPath)) {
                    Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' (Admin/Auth path) is EXPLICITLY BLOCKED.");
                    abort(404, "Halaman '{$path}' tidak dapat diakses di port Apoteker (8001).");
                }
            }

            // Daftar path yang SECARA EKSPLISIT DIIZINKAN di port 8001
            $allowedTokoPaths = [
                '/',         // Halaman utama Toko
                'produk',    // Halaman daftar produk jika ada (misal /produk)
                'produk/*'   // Halaman detail produk (misal /produk/nama-obat)
            ];

            $isAllowed = false;
            foreach ($allowedTokoPaths as $allowedPath) {
                if ($request->is(trim($allowedPath, '/')) || $request->is(trim($allowedPath, '/') . '/*')) {
                    $isAllowed = true;
                    break;
                }
            }

            if ($isAllowed) {
                Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' is ALLOWED for Apoteker.");
                return $next($request); // Jika path diizinkan, lanjutkan dan keluar dari middleware
            }

            // Jika path tidak ada di daftar yang dilarang dan tidak ada di daftar yang diizinkan,
            // anggap sebagai path tidak dikenal dan blokir untuk keamanan.
            Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' (Unspecified/Forbidden path) is BLOCKED.");
            abort(404, "Halaman '{$path}' tidak dapat diakses di port Apoteker (8001).");

        }
        // Logika untuk Port Kasir/Admin/Auth (8000)
        elseif ($port == 8000) {
            Log::info("RestrictMiddleware: Processing Kasir Port (8000) rules for path '{$path}'.");
            
            // Path Toko yang seharusnya diblokir di port Kasir/Admin
            $isTokoPathAttempt = $request->is('/') || $request->is('produk') || $request->is('produk/*');

            if ($isTokoPathAttempt) {
                Log::info("RestrictMiddleware: Port 8000 - Path '{$path}' (Apoteker/Toko path) is BLOCKED for Kasir.");
                abort(404, "Halaman Apoteker/Toko ('{$path}') tidak dapat diakses di port Kasir (8000).");
            } else {
                // Semua path lain (misalnya, /admin, /login, dll.) diizinkan untuk Kasir di port 8000.
                Log::info("RestrictMiddleware: Port 8000 - Path '{$path}' is ALLOWED for Kasir.");
                return $next($request); // Jika path diizinkan, lanjutkan dan keluar dari middleware
            }
        }
        // Untuk port lain yang tidak secara spesifik ditangani
        else {
            Log::info("RestrictMiddleware: Port {$port} ('{$path}') not specifically handled, allowing request.");
        }

        // Jika tidak ada kondisi di atas yang mengembalikan response atau abort, lanjutkan permintaan.
        // Namun, idealnya setiap cabang kondisi port harus memiliki return $next($request) atau abort().
        // Baris ini sebagai fallback jika logika port tidak mencakup semuanya (misalnya port selain 8000/8001)
        // atau jika sebuah kondisi port tidak secara eksplisit mengizinkan/memblokir.
        // Untuk port 8000 dan 8001, kita sudah punya return/abort di dalam kondisinya.
        Log::info("RestrictMiddleware EXIT: Path='{$path}' on Port={$port} - Fallback or allowed, proceeding.");
        return $next($request);
    }
}