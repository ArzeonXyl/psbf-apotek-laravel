<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk logging

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Melakukan autentikasi pengguna
        $request->session()->regenerate(); // Regenerasi ID sesi

        $user = Auth::user(); // Dapatkan instance user yang baru login

        Log::info("User '{$user->email}' with role '{$user->role}' logged in successfully.");

        if ($user->role === 'admin') {
            Log::info("Redirecting admin '{$user->email}' to Filament dashboard.");
            // Lebih spesifik ke dashboard Filament
            return redirect()->intended('/admin');
        }

        if ($user->role === 'apoteker') {
            Log::info("Redirecting apoteker '{$user->email}' to Apoteker dashboard.");
            return redirect()->intended('/apoteker/dashboard');
        }
        

        // Fallback jika role tidak dikenali atau tidak ada role
        // Anda bisa memilih untuk mengarahkan ke halaman default atau ke halaman login lagi dengan error
        Log::warning("User '{$user->email}' has an unrecognized role '{$user->role}'. Redirecting to default home.");
        Auth::logout(); // Opsional: Logout jika role tidak dikenali untuk keamanan
        return redirect('/')->with('error', 'Peran pengguna tidak dikenali.'); // Redirect ke halaman utama dengan pesan error
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); // Logout user dari guard 'web'

        $request->session()->invalidate(); // Invalidasi sesi

        $request->session()->regenerateToken(); // Regenerasi token CSRF

        Log::info("User logged out successfully.");
        // Setelah logout, arahkan ke halaman utama toko (port 8001).
        // Middleware RestrictRoutesByPortMiddleware akan memastikan ini bekerja sesuai port.
        return redirect('/');
    }
}