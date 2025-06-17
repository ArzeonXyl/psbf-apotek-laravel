<?php

namespace App\Livewire\Toko;

use App\Models\Obat;
use Livewire\Component;
use Livewire\WithPagination;

class ObatIndex extends Component
{
    use WithPagination;

    public $obat; // Ini akan berisi koleksi obat yang dipaginasi

    public function mount()
    {
        // Ambil semua obat dengan paginasi
        $this->obat = Obat::paginate(10); // Contoh paginasi, Anda bisa sesuaikan jumlahnya
    }

    // Tidak ada lagi fungsi addToCart, updateQuantity, dll.

    public function render()
    {
        return view('livewire.toko.obat-index', [
            'allObat' => $this->obat, // Menggunakan nama variabel yang jelas di view
        ])->layout('layouts.layout-toko'); // Tetap gunakan layout toko Anda
    }
}