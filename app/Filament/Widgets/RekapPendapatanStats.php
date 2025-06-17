<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RekapPendapatanStats extends BaseWidget
{
    protected static ?int $sort = 1; // Tampil setelah chart tren transaksi

    protected function getStats(): array
    {
        // Fungsi untuk memformat angka menjadi format Rupiah
        $formatRupiah = fn ($value) => 'Rp ' . number_format($value, 0, ',', '.');

        // 1. Hitung pendapatan hari ini
        $pendapatanHariIni = Order::query()
            ->where('status', 'selesai')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // 2. Hitung pendapatan minggu ini (Senin - Minggu)
        $pendapatanMingguIni = Order::query()
            ->where('status', 'selesai')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');

        // 3. Hitung pendapatan bulan ini
        $pendapatanBulanIni = Order::query()
            ->where('status', 'selesai')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // 4. Hitung pendapatan tahun ini
        $pendapatanTahunIni = Order::query()
            ->where('status', 'selesai')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        return [
            Stat::make('Pendapatan Hari Ini', $formatRupiah($pendapatanHariIni))
                ->description('Total dari semua transaksi selesai hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pendapatan Minggu Ini', $formatRupiah($pendapatanMingguIni))
                ->description('Total dari Senin hingga hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pendapatan Bulan Ini', $formatRupiah($pendapatanBulanIni))
                ->description('Total untuk bulan ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pendapatan Tahun Ini', $formatRupiah($pendapatanTahunIni))
                ->description('Total untuk tahun ' . Carbon::now()->year)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}