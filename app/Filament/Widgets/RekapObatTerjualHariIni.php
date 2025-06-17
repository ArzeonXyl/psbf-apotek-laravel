<?php

namespace App\Filament\Widgets;

use App\Models\Obat;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class RekapObatTerjualHariIni extends BaseWidget
{
    protected static ?string $heading = 'Rekapitulasi Obat Terjual (Hari Ini)';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3; // Tampil setelah rekap pendapatan

    public function table(Table $table): Table
    {
        return $table
            // Query untuk mengambil data obat dan menjumlahkan item yang terjual hari ini
            ->query(
                Obat::query()
                    // Menggunakan withSum untuk menghitung jumlah 'quantity' dari relasi 'orderItems'
                    // dengan kondisi tertentu pada relasi 'order'
                    ->withSum(['orderItems as terjual_hari_ini' => function (Builder $query) {
                        $query->whereHas('order', function (Builder $subQuery) {
                            $subQuery->where('status', 'selesai')
                                     ->whereDate('created_at', Carbon::today());
                        });
                    }], 'quantity')
                    // Hanya tampilkan obat yang pernah terjual hari ini atau yang stoknya perlu dipantau
                    ->where(function (Builder $query) {
                        $query->whereHas('orderItems', function (Builder $subQuery) {
                            $subQuery->whereHas('order', function (Builder $orderQuery) {
                                $orderQuery->where('status', 'selesai')->whereDate('created_at', Carbon::today());
                            });
                        })
                        // Anda bisa menambahkan kondisi lain, misal ->orWhere('JUMLAH_STOCK', '<', 10)
                        ;
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('NAMA_OBAT')
                    ->label('Nama Obat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('JUMLAH_STOCK')
                    ->label('Sisa Stok Saat Ini')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('terjual_hari_ini')
                    ->label('Terjual Hari Ini')
                    ->numeric()
                    ->sortable()
                    ->default(0), // Jika tidak ada penjualan, tampilkan 0
            ])
            ->paginated(false); // Matikan paginasi jika ingin menampilkan semua dalam satu tabel
    }
}