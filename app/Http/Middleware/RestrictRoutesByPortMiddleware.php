<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RestrictRoutesByPortMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $port = $request->getPort();
        $path = $request->path();
        // Normalisasi path: jika path adalah '/', ubah menjadi 'slash' agar log lebih jelas
        $logPath = ($path === '/') ? '/' : $path;
        Log::info("RestrictMiddleware ENTRY: Port={$port}, Path='{$logPath}', UserID=" . (Auth::id() ?? 'Guest'));

        // --- Aturan Bypass Umum (Berlaku untuk semua port) ---
        // Izinkan aset, debug, livewire, dan broadcasting auth tanpa pengecekan lebih lanjut.
        if ($request->is(['build/*', '_ignition/*', 'livewire/*', 'broadcasting/auth'])) {
            Log::info("RestrictMiddleware: BYPASS for core assets/livewire/debug path '{$logPath}'.");
            return $next($request);
        }

        // --- ATURAN BERDASARKAN PATH YANG DIAKSES ---

        // 1. Aturan khusus untuk ROOT path ('/')
        // Izinkan akses ke root path HANYA dari port 8000 atau 8001
        if ($request->is('/')) {
            if (in_array($port, [8000, 8001])) {
                Log::info("RestrictMiddleware: ALLOWED - Root path '/' accessed on allowed port {$port}.");
                return $next($request); // Loloskan permintaan
            } else {
                // Jika akses root dari port lain, tolak.
                Log::warning("RestrictMiddleware: BLOCKED - Root path '/' accessed on wrong port {$port}.");
                abort(404, 'Halaman tidak tersedia di port ini.');
            }
        }

        // 2. Jika mencoba akses area ADMIN/KASIR (Filament)
        // Path ini tidak lagi mencakup '/'
        if ($request->is('admin') || $request->is('admin/*')) {
            // Area ini HANYA boleh diakses via port 8000
            if ($port !== 8000) {
                Log::warning("RestrictMiddleware: BLOCKED - Admin path '{$logPath}' accessed on wrong port {$port}.");
                abort(404, 'Halaman Admin tidak tersedia di port ini.');
            }
            // Jika sudah login, pastikan perannya benar
            if (Auth::check() && !in_array(Auth::user()->role, ['admin', 'kasir'])) {
                Log::warning("RestrictMiddleware: BLOCKED - User " . Auth::id() . " with role '" . Auth::user()->role . "' tried to access admin path.");
                abort(403, 'Akses Ditolak untuk Peran Anda.');
            }
        }
        // 3. Jika mencoba akses area APOTEKER
        // Path ini tidak lagi mencakup '/'
        elseif ($request->is('apoteker') || $request->is('apoteker/*')) {
            // Area ini HANYA boleh diakses via port 8001
            if ($port !== 8001) {
                Log::warning("RestrictMiddleware: BLOCKED - Apoteker path '{$logPath}' accessed on wrong port {$port}.");
                abort(404, 'Halaman Apoteker tidak tersedia di port ini.');
            }
            // Jika sudah login, pastikan perannya benar
            if (Auth::check() && Auth::user()->role !== 'apoteker') {
                Log::warning("RestrictMiddleware: BLOCKED - User " . Auth::id() . " with role '" . Auth::user()->role . "' tried to access apoteker path.");
                abort(403, 'Akses Ditolak untuk Peran Anda.');
            }
        }

        // Jika path yang diakses bukan path spesifik di atas (misalnya /login, /logout, /profile),
        // maka permintaan akan diloloskan.
        Log::info("RestrictMiddleware EXIT: Path='{$logPath}' on Port={$port} - Allowed to proceed.");
        return $next($request);
    }
}