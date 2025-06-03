<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Meskipun tidak digunakan di method index, baik untuk ada
use Illuminate\View\View;    // Import View untuk type hinting (opsional tapi baik)

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama Toko.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Mengembalikan view 'home.blade.php' yang ada di dalam folder 'resources/views/toko/'
        return view('toko.home');
    }
}