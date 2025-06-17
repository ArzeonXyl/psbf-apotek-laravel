<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Toko\HomeController;
use App\Http\Controllers\Toko\ProductController;
use App\Livewire\Apoteker\Dashboard;
use App\Livewire\Toko\TokoLandingPage;
use App\Livewire\Apoteker\OrderDashboard; // Pastikan ini sudah dibuat jika digunakan


// ==========================
// PUBLIC ROUTES (Dapat diakses di Port 8001 - Toko)
// ==========================

// Route utama untuk frontend toko (port 8001)
// Middleware RestrictRoutesByPortMiddleware akan memastikannya hanya dapat diakses di port 8001
Route::get('/', TokoLandingPage::class)->name('toko.landing');
Route::get('/produk/{product}', [ProductController::class, 'show'])->name('toko.produk.show');

// Test koneksi Laravel Reverb (untuk debug Echo)
Route::get('/test-reverb-connection', function () {
    $host = env('REVERB_HOST', '127.0.0.1');
    $port = env('REVERB_PORT', 8080);

    try {
        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$socket) {
            Log::error("Failed to connect to Reverb: $errstr ($errno)");
            return "❌ Koneksi ke Reverb GAGAL: $errstr ($errno)";
        } else {
            fclose($socket);
            Log::info("Successfully connected to Reverb at $host:$port");
            return "✅ Koneksi ke Reverb BERHASIL: $host:$port";
        }
    } catch (\Exception $e) {
        Log::error("Exception during Reverb connection test: " . $e->getMessage());
        return "❌ Exception: " . $e->getMessage();
    }
});

// ==========================
// PROTECTED ROUTES (Membutuhkan Login)
// ==========================

Route::middleware(['auth', 'verified'])->group(function () {
    // Route '/dashboard' ini menjadi kurang relevan karena
    // AuthenticatedSessionController sudah melakukan redirect spesifik.
    // Anda bisa menghapusnya, atau biarkan sebagai fallback halaman default
    // jika ada user yang tidak masuk kriteria redirect di controller.
    // Jika ada user tanpa role 'admin' atau 'apoteker', mereka akan kesini.
    Route::get('/dashboard', function() {
        // Ini bisa menjadi halaman dashboard umum atau halaman error jika role tidak terdefinisi
        return view('dashboard'); // Pastikan Anda memiliki view 'dashboard.blade.php'

    })->name('dashboard');

    // Profile routes (umum untuk semua user yang login)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/apoteker/orders', OrderDashboard::class)->name('apoteker.orders.dashboard');
});

// ==========================
// APOTEKER ROUTES (Membutuhkan Role Apoteker & Akses Port 8001)
// ==========================
// Catatan: Middleware 'role:apoteker' akan memblokir jika user bukan apoteker.
// Middleware 'RestrictRoutesByPortMiddleware' akan memblokir jika diakses dari port 8000.
Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', Dashboard::class)->name('apoteker.dashboard');
    Route::get('/apoteker/obat', Dashboard::class)->name('apoteker.obat'); // Jika Dashboard berisi daftar obat
    Route::get('/apoteker/orders', OrderDashboard::class)->name('apoteker.orders'); // Asumsi OrderDashboard adalah Order List
    // Tambahkan rute apoteker lainnya di sini
});

// ==========================
// AUTH ROUTES (Dari Laravel Breeze)
// ==========================
require __DIR__ . '/auth.php';

// ==========================
// FILAMENT ADMIN PANEL ROUTES
// ==========================
// Filament otomatis menangani rute untuk '/admin' dan sub-rutenya.
// Anda tidak perlu mendefinisikannya secara eksplisit di sini.
// Middleware `RestrictRoutesByPortMiddleware` Anda sudah dikonfigurasi
// untuk mengizinkan akses ke '/filament' dan '/filament/*' di port 8000,
// dan memblokirnya di port 8001.