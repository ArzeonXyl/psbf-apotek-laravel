<?php

namespace App\Filament\Widgets; // Pastikan namespace ini sesuai dengan lokasi file Anda

use App\Models\Order; // Import model Order untuk mengambil data transaksi
use Filament\Widgets\ChartWidget;
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal
use Illuminate\Support\Facades\DB; // Import DB facade untuk raw query

class TransaksiHarianChart extends ChartWidget
{
    // Judul yang akan tampil di atas chart
    protected static ?string $heading = 'Tren Transaksi Selesai';

    // Deskripsi di bawah judul (opsional)
    protected static ?string $description = 'Menampilkan jumlah nominal transaksi yang berstatus "selesai".';
    
    // Properti untuk menyimpan filter yang sedang aktif
    public ?string $filter = 'week'; // Filter default saat pertama kali dibuka

    // Agar widget ini mengambil lebar penuh di layoutnya
    protected int | string | array $columnSpan = 'full';

    // Atur urutan widget jika ada beberapa, angka lebih kecil tampil lebih dulu
    protected static ?int $sort = 1;

    /**
     * Method utama untuk mengambil dan memformat data untuk Chart.js
     */
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        // Tentukan tanggal mulai berdasarkan filter yang dipilih di pojok kanan atas chart
        $startDate = match ($activeFilter) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subWeek(),
        };

        // Mengambil data dari tabel 'orders'
        $data = Order::query()
            ->where('status', 'selesai') // Hanya ambil transaksi yang sudah selesai
            ->where('created_at', '>=', $startDate) // Berdasarkan rentang waktu filter
            ->select(
                DB::raw('DATE(created_at) as date'), // Ambil tanggalnya saja
                DB::raw('SUM(total_amount) as aggregate') // Jumlahkan total amount per hari
            )
            ->groupBy('date') // Kelompokkan berdasarkan tanggal
            ->orderBy('date', 'asc') // Urutkan dari tanggal terlama
            ->get();
        
        // Format data agar bisa dibaca oleh Chart.js
        // Buat label untuk sumbu X (misal: 18 Jun, 19 Jun, dst.)
        $labels = $data->map(fn ($item) => Carbon::parse($item->date)->translatedFormat('d M'));
        // Ambil nilai untuk sumbu Y (total transaksi per hari)
        $values = $data->map(fn ($item) => $item->aggregate);

        return [
            'datasets' => [
                [
                    'label' => 'Total Transaksi (Rp)',
                    'data' => $values,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)', // Warna area di bawah garis
                    'borderColor' => 'rgb(54, 162, 235)', // Warna garis
                    'tension' => 0.3, // Membuat garis sedikit melengkung (tidak kaku)
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Menentukan tipe chart. Ini sudah di-set saat kita memilih 'Line chart' tadi.
     */
    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Menambahkan filter rentang waktu di pojok kanan atas widget chart
     */
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => '7 Hari Terakhir',
            'month' => '30 Hari Terakhir',
            'year' => 'Tahun Ini',
        ];
    }
}