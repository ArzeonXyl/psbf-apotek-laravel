<?php

namespace App\Livewire\Toko;

use App\Models\Obat; // Menggunakan model Obat yang sudah kita buat
use Livewire\Component;
use Livewire\WithPagination; // Untuk fitur paginasi

class ProductList extends Component
{
    use WithPagination; // Mengaktifkan paginasi

    public string $search = ''; // Properti untuk menampung input pencarian
    protected $paginationTheme = 'tailwind'; // Menggunakan tema Tailwind untuk tampilan paginasi

    // Baris ini akan membuat query string pencarian muncul di URL (?search=...)
    // dan menjaga nilai pencarian saat halaman di-refresh atau navigasi paginasi.
    protected $queryString = ['search'];

    public function render()
    {
        // Query untuk mengambil data obat dari database
        $obats = Obat::query() // Memulai query pada model Obat
            ->when($this->search, function ($query, $searchTerm) {
                // Jika properti $search tidak kosong, tambahkan kondisi where
                // Cari berdasarkan NAMA_OBAT atau KETERANGAN (bisa Anda sesuaikan)
                return $query->where('NAMA_OBAT', 'like', '%' . $searchTerm . '%')
                             ->orWhere('KETERANGAN', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('NAMA_OBAT', 'asc') // Urutkan hasil berdasarkan NAMA_OBAT secara ascending
            ->paginate(9); // Ambil data dengan paginasi, misalnya 9 item per halaman

        // Mengirim data $obats ke view komponen Livewire
        return view('livewire.toko.product-list', [
            'obats' => $obats,
        ]);
    }

    /**
     * Method ini akan dipanggil otomatis oleh Livewire setiap kali
     * properti $search diupdate (karena kita menggunakan wire:model.live).
     * Tujuannya adalah untuk mereset paginasi ke halaman pertama
     * setiap kali ada pencarian baru, agar hasilnya akurat.
     */
    public function updatedSearch()
    {
        $this->resetPage(); // Mereset paginasi ke halaman 1
    }
}