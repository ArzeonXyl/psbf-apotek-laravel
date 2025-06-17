<?php

namespace App\Livewire\Apoteker;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DashboardLayout extends Component
{
    public $sidebarOpen = false;

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/login'); // Redirect ke halaman login setelah logout
    }

    public function render()
    {
        return view('livewire.apoteker.dashboard-layout');
    }
} 