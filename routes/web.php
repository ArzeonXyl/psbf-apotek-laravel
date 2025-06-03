<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RestrictRoutesByPortMiddleware;
use App\Http\Controllers\Toko\HomeController;
use App\Http\Controllers\Toko\ProductController;

Route::get('/', function () {
    return view('welcome');
});

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