<?php

namespace App\Http\Middleware; // Pastikan namespace ini BENAR!

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika pengguna belum login, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect('/login'); // Atau route lain sesuai kebutuhan
        }

        $user = Auth::user();

        // Log untuk debugging: Apa role user yang sedang di cek dan role yang dibutuhkan
        \Log::info("Checking role for user: {$user->email}. User role: {$user->role}. Required roles: " . implode(', ', $roles));


        // Cek apakah user memiliki salah satu peran yang dibutuhkan
        if (!in_array($user->role, $roles)) {
            // Jika tidak memiliki peran yang sesuai, kembalikan error atau redirect
            \Log::warning("Access denied for user: {$user->email}. User role: {$user->role}. Required roles: " . implode(', ', $roles));

            // Contoh: Kembalikan respons 403 Forbidden
            abort(403, 'Unauthorized access. You do not have the necessary role to access this page.');

            // Atau redirect ke halaman lain, misalnya halaman utama
            // return redirect('/');
        }

        return $next($request);
    }
}