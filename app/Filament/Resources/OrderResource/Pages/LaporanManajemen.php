<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
// Import semua widget Anda (pastikan path-nya benar sesuai lokasi file widget Anda)
use App\Filament\Widgets\TransaksiHarianChart;
use App\Filament\Widgets\RekapPendapatanStats;
use App\Filament\Widgets\RekapObatTerjualHariIni;

class LaporanManajemen extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string $view = 'filament.pages.laporan-manajemen';
    protected static ?string $slug = 'laporan-manajemen';
    protected static ?string $navigationLabel = 'Laporan Manajemen';
    protected static ?string $title = 'Laporan Manajemen';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RekapPendapatanStats::class,
            TransaksiHarianChart::class,
            RekapObatTerjualHariIni::class,
        ];
    }
    
    /**
     * Mengatur layout kolom untuk widget di atas.
     * HARUS PUBLIC
     */
    public function getHeaderWidgetsColumns(): int | array // <-- PERBAIKAN DI SINI
    {
        return 2;
    }
}