<?php

// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

// ... (channel App.Models.User.{id} jika ada) ...

// Broadcast::channel('filament.orders', function ($user) {
//     Log::info('Attempting to authorize filament.orders channel for user:', ['user_id' => $user->id ?? 'guest', 'roles' => $user ? implode(', ', $user->getRoleNames()->toArray()) : 'none']); // Log roles

//     // Contoh: Izinkan user yang memiliki peran 'admin' atau 'kasir'
//     $isAuthorized = ($user && ($user->hasRole('admin') || $user->hasRole('kasir'))); // Sesuaikan dengan implementasi peran Anda

//     Log::info('Authorization result for filament.orders:', ['user_id' => $user->id ?? 'guest', 'can_access' => $isAuthorized]);
//     return $isAuthorized;
// });
Broadcast::channel('filament.orders', function () {
    return true;
});
