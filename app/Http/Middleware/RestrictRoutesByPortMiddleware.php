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
        $path = $request->path(); // Mendapatkan path URI tanpa leading slash
        Log::info("RestrictRoutesByPortMiddleware ENTRY: Port={$port}, Path='{$path}'");

        // --- Aturan Umum: Selalu Izinkan untuk Assets & Autentikasi Internal ---

        // 1. Izinkan akses untuk assets (CSS, JS) yang di-handle oleh Vite atau aset publik lainnya
        // Path `build/` untuk Vite assets. Anda mungkin perlu menambahkan path lain seperti `_ignition/*` untuk debug Laravel.
        // Juga izinkan root path '/' untuk halaman utama toko.
        if (str_starts_with($path, 'build/') || str_starts_with($path, '_ignition/') || $path === '/') {
             Log::info("RestrictRoutesByPortMiddleware: Bypass for asset, debug, or root path '{$path}'.");
             return $next($request);
        }

        // 2. Izinkan akses untuk rute Livewire (termasuk AJAX calls-nya) dan Broadcasting Auth (Reverb)
        if (str_starts_with($path, 'livewire/') || $path === 'broadcasting/auth') {
            Log::info("RestrictRoutesByPortMiddleware: Bypass for Livewire or Broadcasting Auth for '{$path}'.");
            return $next($request);
        }

        // 3. Izinkan rute otentikasi (login, register, logout, dll.) di semua port
        // Ini agar user bisa mengakses form login dari port mana pun (Breeze login, Filament login).
        // Redirect setelah login akan ditangani oleh AuthenticatedSessionController.
        // Path Filament login juga perlu diizinkan di sini agar tidak diblokir.
        if ($request->is('login') || $request->is('register') || $request->is('forgot-password') ||
            $request->is('reset-password/*') || $request->is('verify-email') || $request->is('email/verify/*') ||
            $request->is('confirm-password') || $request->is('logout') ||
            // Untuk Filament login:
            $request->is('admin/login') || $request->is('admin/password/reset') || $request->is('admin/password/email')) {
            Log::info("RestrictRoutesByPortMiddleware: Bypass for Auth routes on '{$path}'.");
            return $next($request);
        }

        // 4. Izinkan rute profile dan default dashboard (setelah login)
        // Ini adalah rute yang bisa diakses oleh *siapa saja yang sudah login*
        // terlepas dari role mereka, selama mereka ada di port yang benar
        if (Auth::check() && (str_starts_with($path, 'profile') || $path === 'dashboard')) {
            Log::info("RestrictRoutesByPortMiddleware: Bypass for authenticated profile/dashboard routes on '{$path}'.");
            return $next($request);
        }


        // --- Aturan Berdasarkan Port dan Role ---

        if ($port == 8001) { // Port Frontend Toko (Apoteker)
            Log::info("RestrictRoutesByPortMiddleware: Port 8001 (Apoteker) rules in action for path '{$path}'.");

            // Pastikan user adalah apoteker jika sudah login dan mencoba mengakses rute apoteker/toko
            if (Auth::check()) {
                if (Auth::user()->role !== 'apoteker') {
                    Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - User " . Auth::user()->email . " is not 'apoteker' but accessed port 8001 for path '{$path}'. Logging out.");
                    Auth::logout(); // Log out user dengan role salah
                    return redirect()->route('login')->with('error', 'Akses tidak diizinkan untuk peran Anda di port ini.');
                }
            } else {
                // Jika belum login, dan bukan rute auth yang diizinkan (sudah dihandle di atas),
                // dan mencoba mengakses rute apoteker (yang seharusnya butuh login).
                if (str_starts_with($path, 'apoteker/')) {
                    Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Unauthenticated user trying to access apoteker routes on port 8001 for path '{$path}'.");
                    return redirect()->route('login')->with('error', 'Anda harus login sebagai Apoteker untuk mengakses ini.');
                }
            }

            // Path yang DIIZINKAN secara eksplisit untuk Apoteker di port 8001
            $allowedApotekerPaths = [
                'apoteker/dashboard',
                'apoteker/obat',
                'apoteker/orders',
                'produk', // Rute public produk juga bisa diulang di sini untuk kejelasan
                'produk/*',
                // Tambahkan path spesifik apoteker/toko lainnya
            ];

            // Jika path adalah rute apoteker yang diizinkan, lanjutkan
            foreach ($allowedApotekerPaths as $allowedPath) {
                if (str_starts_with($path, $allowedPath)) {
                    Log::info("RestrictRoutesByPortMiddleware: ALLOWED on port 8001 for path '{$path}'.");
                    return $next($request);
                }
            }

            // Jika path ini adalah rute yang seharusnya hanya untuk admin/filament, blokir.
            if (str_starts_with($path, 'admin') || str_starts_with($path, 'filament')) { // 'admin' juga perlu diblokir
                Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Admin/Filament path '{$path}' accessed on port 8001.");
                abort(404, "Halaman '{$path}' hanya untuk Admin, tidak bisa diakses dari port Apoteker (8001).");
            }

            // Jika sampai sini, berarti path tidak termasuk di yang diizinkan untuk apoteker,
            // dan bukan rute public toko yang sudah diizinkan di atas. Ini adalah default block.
            Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Path '{$path}' not explicitly allowed for Apoteker/Toko on port 8001.");
            abort(404, "Halaman '{$path}' tidak boleh diakses di port Apoteker (8001).");

        } elseif ($port == 8000) { // Port Backend Admin (Filament)
            Log::info("RestrictRoutesByPortMiddleware: Port 8000 (Admin) rules in action for path '{$path}'.");

            // Pastikan user adalah admin jika sudah login dan mencoba mengakses rute admin
            if (Auth::check()) {
                if (Auth::user()->role !== 'admin') {
                    Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - User " . Auth::user()->email . " is not 'admin' but accessed port 8000 for path '{$path}'. Logging out.");
                    Auth::logout(); // Log out user dengan role salah
                    return redirect()->route('login')->with('error', 'Akses tidak diizinkan untuk peran Anda di port ini.');
                }
            } else {
                 // Jika belum login dan mencoba mengakses rute admin/filament, arahkan ke login Filament
                if (str_starts_with($path, 'filament') || str_starts_with($path, 'admin')) { // 'admin' juga perlu di sini
                    Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Unauthenticated user trying to access admin routes on port 8000 for path '{$path}'.");
                    return redirect('/admin/login')->with('error', 'Anda harus login sebagai Admin untuk mengakses ini.');
                }
            }

            // Path yang DIIZINKAN secara eksplisit untuk Admin di port 8000
            // Filament akan menangani otorisasi internalnya sendiri setelah ini
            $allowedAdminPaths = [
                'admin', // Mengizinkan /admin dan /admin/dashboard
                'filament', // Mengizinkan /filament dan semua sub-pathnya
                // Tambahkan rute kustom admin non-Filament jika ada
            ];

            foreach ($allowedAdminPaths as $allowedPath) {
                 if (str_starts_with($path, $allowedPath)) {
                    Log::info("RestrictRoutesByPortMiddleware: ALLOWED on port 8000 for path '{$path}'.");
                    return $next($request);
                }
            }

            // Jika path ini adalah rute yang seharusnya hanya untuk apoteker/toko, blokir.
            if (str_starts_with($path, 'apoteker/') || $request->is('produk') || $request->is('produk/*') || $request->is('/')) {
                 Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Apoteker/Toko path '{$path}' accessed on port 8000.");
                 abort(404, "Halaman '{$path}' hanya untuk Apoteker/Toko, tidak bisa diakses dari port Admin (8000).");
            }

            // Jika sampai sini, berarti path tidak termasuk di yang diizinkan untuk admin,
            // dan bukan rute public toko yang sudah diizinkan di atas. Ini adalah default block.
            Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Path '{$path}' not explicitly allowed for Admin on port 8000.");
            abort(404, "Halaman '{$path}' tidak boleh diakses di port Admin (8000).");

        }

        // --- Fallback: Jika port tidak 8000 atau 8001 ---
        // Blokir akses jika port tidak dikenali/diizinkan.
        Log::warning("RestrictRoutesByPortMiddleware: BLOCKED - Unknown port {$port}, path '{$path}'.");
        abort(404, "Port '{$port}' tidak dikenali atau tidak diizinkan.");
    }
}