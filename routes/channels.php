<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User; // <-- PASTIKAN MODEL USER DI-IMPORT

/**
 * Otorisasi untuk channel notifikasi privat per pengguna.
 * INI SANGAT PENTING UNTUK MENGATASI ERROR 403 DI FILAMENT.
 * Ini mengizinkan pengguna untuk mendengarkan di channel mereka sendiri (misal: App.Models.User.1).
 */
Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Otorisasi untuk channel 'filament.orders' (versi aman).
 * Hanya mengizinkan pengguna yang sudah login dan memiliki peran tertentu.
 */
Broadcast::channel('filament.orders', function (User $user) {
    // Sesuaikan daftar peran yang boleh mengakses channel ini
    return in_array($user->role, ['admin', 'kasir', 'apoteker']);
});

/**
 * Jika Anda punya channel 'apoteker-channel' untuk notifikasi order baru,
 * Anda bisa menambahkannya juga di sini jika ingin menjadikannya privat.
 */
// Broadcast::channel('apoteker-channel', function (User $user) {
//     return in_array($user->role, ['apoteker', 'admin']);
// });