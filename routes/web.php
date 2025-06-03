<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RestrictRoutesByPortMiddleware;
use App\Http\Controllers\Toko\HomeController;
use App\Http\Controllers\Toko\ProductController;
use App\Livewire\Apoteker\OrderDashboard;
use App\Events\NewOrderCreated; // <-- 1. IMPORT EVENT YANG SUDAH KITA BUAT
use App\Events\OrderStatusUpdated; // <-- 1. IMPORT EVENT YANG SUDAH KITA BUAT
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use Illuminate\Support\Facades\Log;

// <-- 1. IMPORT EVENT YANG SUDAH KITA BUAT
Route::get('/', function () {
    return view('welcome');
});
// Rute untuk Dashboard Order Apoteker
Route::get('/apoteker/orders', OrderDashboard::class)->name('apoteker.orders.dashboard'); // <-- 2. RUTE BARU DITAMBAHKAN
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// port 8001 untuk Toko
Route::get('/', [App\Http\Controllers\Toko\HomeController::class, 'index'])->name('toko.home');
Route::get('/produk/{product}', [ProductController::class, 'show'])
      ->name('toko.produk.show'); // <-- Pastikan ->name('toko.produk.show') ada dan benar

Route::get('/test-reverb-connection', function() {
    $host = env('REVERB_HOST', '127.0.0.1');
    $port = env('REVERB_PORT', 8080);

    try {
        // Mencoba membuat koneksi socket TCP
        $socket = @fsockopen($host, $port, $errno, $errstr, 5); // Timeout 5 detik

        if (!$socket) {
            Log::error("Failed to connect to Reverb: $errstr ($errno)");
            return "Koneksi ke Reverb GAGAL: $errstr ($errno)";
        } else {
            fclose($socket);
            Log::info("Successfully connected to Reverb at $host:$port");
            return "Koneksi ke Reverb BERHASIL pada $host:$port";
        }
    } catch (\Exception $e) {
        Log::error("Exception during Reverb connection test: " . $e->getMessage());
        return "Exception: " . $e->getMessage();
    }
});      