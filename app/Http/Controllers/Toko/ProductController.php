<?php

namespace App\Http\Controllers\Toko; // (1) Namespace sesuai lokasi folder

use App\Http\Controllers\Controller; // (2) Kelas Controller dasar Laravel
use App\Models\Product;             // (3) Model Product kita (untuk mengambil data produk)
use Illuminate\Http\Request;         // (4) Class Request (meskipun tidak langsung dipakai di method show ini, baik untuk ada)
use Illuminate\View\View;            // (5) Class View (untuk type hinting return)

class ProductController extends Controller // (6) Nama kelas controller
{
    /**
     * Menampilkan halaman detail untuk satu produk.
     *
     * @param  \App\Models\Product  $product (Ini adalah Route Model Binding)
     * @return \Illuminate\View\View
     */
    public function show(Product $product): View
    {
        // Laravel akan secara otomatis mencari instance Product berdasarkan ID
        // yang ada di URL (misalnya /produk/1 akan mencari Product dengan ID 1).
        // Variabel $product yang berisi data produk tersebut kemudian dikirim ke view.

        // (7) Mengembalikan view 'show.blade.php' yang ada di folder 'resources/views/toko/produk/'
        // dan mengirimkan data $product ke view tersebut.
        return view('toko.produk.show', compact('product'));
    }

    // Anda bisa menambahkan method lain di sini jika diperlukan untuk Toko
    // Misalnya, method untuk menampilkan semua produk (jika tidak ditangani Livewire sepenuhnya)
    // atau method untuk menangani pencarian (jika tidak ditangani Livewire).
}