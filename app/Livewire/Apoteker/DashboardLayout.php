<?php

namespace App\Livewire\Apoteker;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DashboardLayout extends Component
{
    public $sidebarOpen = false;

    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return $this->redirect('/', navigate: true);
    }
    public function render()
    {
        return view('livewire.apoteker.dashboard-layout');
    }
} 