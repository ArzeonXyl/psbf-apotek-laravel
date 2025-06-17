<?php

namespace App\Livewire\Apoteker;

use Livewire\Component;
use App\Models\Obat;
use Livewire\Attributes\Layout; // <-- Import Atribut Layout
use Livewire\Attributes\Title;  // <-- Import Atribut Title (Opsional)

#[Layout('layouts.dashboard-layout')] // <-- Cara baru di Livewire v3 untuk define layout
#[Title('Dashboard Apoteker')] // <-- Cara baru di Livewire v3 untuk set judul halaman
class Dashboard extends Component
{
    public bool $sidebarOpen = true; // Properti untuk kontrol sidebar

    public function render()
    {
        // Ambil data yang dibutuhkan untuk dashboard
        // Mungkin ringkasan, bukan semua obat. Untuk contoh, kita ambil beberapa.
        $obats = Obat::orderBy('NAMA_OBAT')->limit(5)->get();

        return view('livewire.apoteker.dashboard', [
            'obats' => $obats,
        ]);
    }

    // Fungsi logout jika dipanggil dari layout ini
    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return $this->redirect('/', navigate: true);
    }
}