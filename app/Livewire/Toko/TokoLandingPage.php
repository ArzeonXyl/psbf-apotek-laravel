<?php

namespace App\Livewire\Toko;

use Livewire\Component;
use Illuminate\View\View;
use Livewire\Attributes\Layout; // Import atribut Layout

#[Layout('layouts.layout-toko')] // Menggunakan atribut untuk layout
class TokoLandingPage extends Component
{
    public function render(): View
    {
        // Hanya kembalikan view utama, layout sudah didefinisikan oleh atribut
        return view('livewire.toko.toko-landing-page');
    }
}