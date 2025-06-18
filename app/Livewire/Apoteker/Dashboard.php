<?php

namespace App\Livewire\Apoteker;

use Livewire\Component;
use App\Models\Obat;
use Illuminate\Support\Facades\DB;
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
        $transaksi_obat = DB::table('order_items')
        ->selectRaw('strftime("%m-%Y", created_at) as bulan, SUM(sub_total) as total')
        ->groupBy(DB::raw('strftime("%m-%Y", created_at)'))
        ->orderBy(DB::raw('strftime("%Y-%m", created_at)'))
        ->get();

        $total_pendapatan = DB::table('order_items')
        ->selectRaw('SUM(sub_total) as total_pendapatan')
        ->get();

        $transaksi_daily = DB::table('order_items')
        ->selectRaw('strftime("%d-%m", created_at) as day, COUNT(sub_total) as total_penjualan')
        ->groupBy(DB::raw('strftime("%d-%m", created_at)'))
        ->get();

        $obat_favorit = DB::table('order_items')
            ->join('obat', 'order_items.ID_OBAT', '=', 'obat.ID_OBAT')
            ->select('obat.NAMA_OBAT as nama_obat', DB::raw('COUNT(order_items.ID_OBAT) as jumlah_order'))
            ->groupBy('order_items.ID_OBAT') 
            ->orderByDesc('jumlah_order')
            ->limit(3)
            ->get();


        return view('livewire.apoteker.dashboard', [
            'obats' => $obats,
            'labels_pendapatan' => $transaksi_obat->pluck('bulan'),
            'data_pendapatan' => $transaksi_obat->pluck('total'),
            'label_pengeluaran_daily' => $transaksi_daily->pluck('day'),
            'data_pengeluaran_daily' => $transaksi_daily->pluck('total_penjualan'),
            'total_pendapatan' => $total_pendapatan->pluck('total_pendapatan'),
            'obat_favorit' => $obat_favorit
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