<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RestrictRoutesByPortMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $port = $request->getPort();
        $path = $request->path();
        Log::info("RestrictMiddleware ENTRY: Port={$port}, Path='{$path}', UserID=" . (Auth::id() ?? 'Guest'));

        // --- Aturan Bypass Umum (Berlaku untuk semua port) ---
        // Izinkan aset, debug, livewire, dan broadcasting auth tanpa pengecekan lebih lanjut.
        if ($request->is(['build/*', '_ignition/*', 'livewire/*', 'broadcasting/auth'])) {
            Log::info("RestrictMiddleware: BYPASS for core assets/livewire/debug path '{$path}'.");
            return $next($request);
        }

        // --- ATURAN BERDASARKAN PATH YANG DIAKSES ---

        // 1. Jika mencoba akses area ADMIN/KASIR (Filament)
        if ($request->is('admin') || $request->is('admin/*')|| $request->is('/')) {
            // Area ini HANYA boleh diakses via port 8000
            if ($port !== 8000) {
                Log::warning("RestrictMiddleware: BLOCKED - Admin path '{$path}' accessed on wrong port {$port}.");
                abort(404, 'Halaman Admin tidak tersedia di port ini.');
            }
            // Jika sudah login, pastikan perannya benar
            if (Auth::check() && !in_array(Auth::user()->role, ['admin', 'kasir'])) {
                Log::warning("RestrictMiddleware: BLOCKED - User " . Auth::id() . " with role '" . Auth::user()->role . "' tried to access admin path.");
                abort(403, 'Akses Ditolak untuk Peran Anda.');
            }
        }
        // 2. Jika mencoba akses area APOTEKER
        elseif ($request->is('apoteker') || $request->is('apoteker/*')) {
            // Area ini HANYA boleh diakses via port 8001
            if ($port !== 8001) {
                Log::warning("RestrictMiddleware: BLOCKED - Apoteker path '{$path}' accessed on wrong port {$port}.");
                abort(404, 'Halaman Apoteker tidak tersedia di port ini.');
            }
            // Jika sudah login, pastikan perannya benar
            if (Auth::check() && Auth::user()->role !== 'apoteker') {
                Log::warning("RestrictMiddleware: BLOCKED - User " . Auth::id() . " with role '" . Auth::user()->role . "' tried to access apoteker path.");
                abort(403, 'Akses Ditolak untuk Peran Anda.');
            }
        }
        // 3. Jika mencoba akses area TOKO PUBLIK
        elseif ($request->is('/') || $request->is('produk') || $request->is('produk/*')) {
            // Area ini HANYA boleh diakses via port 8001
            if ($port !== 8001) {
                Log::warning("RestrictMiddleware: BLOCKED - Public Toko path '{$path}' accessed on wrong port {$port}.");
                abort(404, 'Halaman Toko tidak tersedia di port ini.');
            }
        }

        // Jika path yang diakses bukan path spesifik di atas (misalnya /login, /logout, /profile),
        // maka permintaan akan diloloskan.
        // Middleware 'auth' bawaan Laravel yang akan menangani proteksi untuk /profile.
        // Halaman /login bisa diakses dari mana saja.

        Log::info("RestrictMiddleware EXIT: Path='{$path}' on Port={$port} - Allowed to proceed.");
        return $next($request);
    }
}