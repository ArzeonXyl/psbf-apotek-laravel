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
        $path = $request->path();
        Log::info("RestrictMiddleware ENTRY: Port={$port}, Path='{$path}'");

        if ($request->is('livewire/*')) {
            Log::info("RestrictMiddleware: Allowing livewire/* path for '{$path}'.");
            return $next($request);
        }

        if ($port == 8001) { // APOTEKER (TOKO) PORT
            Log::info("RestrictMiddleware: Processing Apoteker Port (8001) rules for path '{$path}'.");

            $forbiddenPathsOnTokoPort = [
                'admin', 'admin/*', 'login', 'register', 'forgot-password', 
                'reset-password', 'email/verification-notification', 
                'verify-email', 'confirm-password', 'dashboard'
            ];

            foreach ($forbiddenPathsOnTokoPort as $forbiddenPath) {
                if ($request->is($forbiddenPath)) {
                    Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' (Admin/Auth path) is EXPLICITLY BLOCKED.");
                    abort(404, "Halaman '{$path}' tidak dapat diakses di port Apoteker (8001).");
                }
            }

            // Path yang diizinkan secara eksplisit untuk Toko/Apoteker di port 8001
            $allowedTokoPaths = [
                '/',               // Halaman utama Toko
                'produk',          // Untuk path /produk
                'produk/*',        // Untuk path /produk/detail-apapun
                'apoteker/orders'  // <-- TAMBAHKAN PATH BARU APOTEKER DI SINI
            ];

            $isAllowed = false;
            foreach ($allowedTokoPaths as $allowedPath) {
                // Menggunakan trim untuk menangani '/' dan path lain dengan benar
                if ($request->is(trim($allowedPath, '/')) || $request->is(trim($allowedPath, '/') . '/*')) {
                    // Khusus untuk '/', $request->is('/') sudah cukup.
                    // Untuk path lain, $request->is(trim($allowedPath, '/')) akan cocok.
                    // Untuk pola seperti 'produk/*', $request->is($allowedPath) sudah benar.
                    // Kita sederhanakan:
                    if ($request->is($allowedPath)) { // $request->is() sudah bisa handle wildcard '*'
                         $isAllowed = true;
                         break;
                    }
                }
            }
            // Penyesuaian untuk path root '/' karena trim akan menghilangkannya
            if (!$isAllowed && in_array('/', $allowedTokoPaths) && $request->is('/')) {
                $isAllowed = true;
            }


            if ($isAllowed) {
                Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' is ALLOWED for Apoteker.");
                return $next($request);
            }

            Log::info("RestrictMiddleware: Port 8001 - Path '{$path}' (Unspecified/Forbidden path) is BLOCKED.");
            abort(404, "Halaman '{$path}' tidak dapat diakses di port Apoteker (8001).");

        } elseif ($port == 8000) { // KASIR (ADMIN/FILAMENT/AUTH) PORT
            // (Logika untuk port 8000 tetap sama seperti sebelumnya)
            Log::info("RestrictMiddleware: Processing Kasir Port (8000) rules for path '{$path}'.");
            $isTokoPathAttempt = $request->is('/') || $request->is('produk') || $request->is('produk/*') || $request->is('apoteker/orders'); // Tambahkan juga path apoteker

            if ($isTokoPathAttempt) {
                Log::info("RestrictMiddleware: Port 8000 - Path '{$path}' (Apoteker/Toko path) is BLOCKED for Kasir.");
                abort(404, "Halaman Apoteker/Toko ('{$path}') tidak dapat diakses di port Kasir (8000).");
            } else {
                Log::info("RestrictMiddleware: Port 8000 - Path '{$path}' is ALLOWED for Kasir.");
                return $next($request);
            }
        } else {
            Log::info("RestrictMiddleware: Port {$port} ('{$path}') not specifically handled, allowing request.");
        }

        // Fallback jika tidak ada return/abort eksplisit di atas (seharusnya tidak tercapai untuk port 8000/8001)
        Log::info("RestrictMiddleware EXIT: Path='{$path}' on Port={$port} - Fallback or allowed, proceeding.");
        return $next($request);
    }
}