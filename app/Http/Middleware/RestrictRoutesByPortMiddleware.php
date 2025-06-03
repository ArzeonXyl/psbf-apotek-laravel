<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; // Pastikan Response di-import

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

        // Izinkan semua request ke endpoint Livewire lewat dulu untuk kesederhanaan.
        // Idealnya, penanganan Livewire juga bisa lebih spesifik,
        // tapi untuk awal ini kita biarkan agar tidak memblokir fungsi Livewire di Toko maupun Filament.
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        // Aturan untuk Port 8001 (Toko)
        if ($port == 8001) {
            // Path yang diizinkan untuk Toko
            $isTokoPath = $request->is('/') || $request->is('produk/*');
            // Path yang seharusnya tidak diakses di port Toko
            $isNonTokoPathAttempt = $request->is('admin*') ||
                                    $request->is('login') ||
                                    $request->is('register') ||
                                    $request->is('dashboard'); // Dashboard Breeze

            if (!$isTokoPath && $isNonTokoPathAttempt) {
                abort(404, 'Halaman Admin/Autentikasi tidak dapat diakses di port Toko (8001).');
            }
            // Jika bukan path Toko dan bukan path Admin/Auth (misal path aneh), biarkan routing Laravel yang handle
            // atau bisa juga langsung abort(404) jika ingin lebih ketat hanya $isTokoPath yang boleh.
            // Untuk sekarang, kita fokus memblokir yang jelas salah.

        }
        // Aturan untuk Port 8000 (Admin/Kasir/Autentikasi)
        elseif ($port == 8000) {
            // Path Toko yang seharusnya tidak diakses di port Admin/Kasir
            $isTokoPathAttempt = $request->is('/') || $request->is('produk/*');

            if ($isTokoPathAttempt) {
                abort(404, 'Halaman Toko tidak dapat diakses di port Admin/Kasir (8000).');
            }
            // Rute lain seperti /admin, /login, /dashboard akan diizinkan secara implisit
            // karena tidak ada kondisi blokir spesifik untuk mereka di port 8000 ini.
        }

        // Jika tidak ada aturan blokir yang cocok di atas, lanjutkan permintaan
        return $next($request);
    }
}