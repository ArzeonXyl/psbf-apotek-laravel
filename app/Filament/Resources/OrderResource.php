<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Obat; // Import model Obat
use App\Models\OrderItem; // Import model OrderItem
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Import komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get; // Untuk mendapatkan value field lain secara reaktif
use Filament\Forms\Set; // Untuk mengisi value field lain secara reaktif
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan ID user yang login
use Filament\Forms\Components\DatePicker; // Import DatePicker untuk filter
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Input Transaksi Kasir';
    protected static ?string $pluralModelLabel = 'Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $slug = 'transaksi-kasir';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(), // Menggunakan seluruh lebar kolom

                Repeater::make('items')
                    ->relationship() // Memberitahu Repeater untuk mengelola relasi dengan OrderItem
                    ->label('Item Obat')
                    ->schema([
                        Select::make('ID_OBAT')
                            ->label('Nama Obat')
                            ->options(Obat::query()->pluck('NAMA_OBAT', 'ID_OBAT')) // Mengambil opsi dari model Obat
                            ->searchable()
                            ->required()
                            ->reactive() // Penting untuk update harga otomatis
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                // Ketika obat dipilih, update harga satuan dan hitung sub_total
                                $obat = Obat::find($state);
                                if ($obat) {
                                    $set('price_at_purchase', (float) $obat->HARGA); // Pastikan float
                                    $quantity = (int) $get('quantity'); // Ambil kuantitas yang sudah ada (default 1)
                                    $set('sub_total', (float) ($obat->HARGA * $quantity)); // Hitung sub_total
                                } else {
                                    // Jika obat tidak ditemukan, set ke 0
                                    $set('price_at_purchase', 0.00);
                                    $set('sub_total', 0.00);
                                }
                            })
                            // Dehydrate hanya jika ID_OBAT terisi (ini adalah default Filament, tapi ditegaskan)
                            ->dehydrated(fn ($state) => filled($state)),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->default(1) // Default jumlah adalah 1
                            ->minValue(1) // Minimal 1
                            ->reactive()
                            ->live(debounce: 300) // Update secara real-time dengan debounce 300ms
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                // Ketika kuantitas berubah, hitung ulang sub_total
                                $price = (float) $get('price_at_purchase'); // Ambil harga satuan
                                $quantity = (int) $state; // Ambil kuantitas yang baru
                                $set('sub_total', (float) ($price * $quantity)); // Hitung sub_total
                            })
                            ->dehydrated(), // Pastikan kuantitas tersimpan

                        TextInput::make('price_at_purchase')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // Harga diambil otomatis, tidak bisa diubah
                            ->dehydrated() // Pastikan nilai tetap tersimpan meskipun disabled
                            ->default(0.00), // Default 0.00 jika belum ada harga

                        TextInput::make('sub_total')
                            ->label('Sub Total')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // Sub total dihitung otomatis, tidak bisa diubah
                            ->dehydrated() // Pastikan nilai tetap tersimpan meskipun disabled
                            ->default(0.00), // Default 0.00
                    ])
                    ->addActionLabel('Tambah Obat') // Label tombol tambah item
                    ->columns(4) // Layout kolom di dalam repeater
                    ->defaultItems(1) // Default ada 1 item saat form dibuka
                    ->collapsible() // Bisa dilipat
                    ->cloneable() // Bisa diduplikasi
                    ->reorderable(false) // Tidak bisa diurutkan ulang
                    ->live(), // Penting: ini memicu placeholder total untuk update real-time

                // Placeholder untuk menampilkan Total Pembayaran sementara (sebelum disimpan)
                Placeholder::make('total_amount_display')
                    ->label('Total Pembayaran')
                    ->content(function (Get $get): string {
                        $total = 0.00; // Inisialisasi dengan float
                        $items = $get('items'); // Ambil semua item dari repeater
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                // Jumlahkan sub_total dari setiap item, pastikan konversi ke float
                                $total += (float) ($item['sub_total'] ?? 0.00);
                            }
                        }
                        return 'Rp ' . number_format($total, 0, ',', '.'); // Format ke Rupiah
                    })
                    ->reactive(), // Agar placeholder ini ikut update secara real-time

                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(), // Menggunakan seluruh lebar kolom
            ])->columns(1); // Layout kolom untuk form utama
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID Order')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Nama Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Kasir')->sortable(), // Menampilkan nama kasir dari relasi
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric( // Menggunakan numeric formatter untuk presisi
                        decimalPlaces: 2,
                        thousandsSeparator: '.',
                        decimalSeparator: ','
                    )
                    ->money('IDR', locale: 'id') // Format sebagai mata uang IDR
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->searchable()->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'warning', // Warna badge untuk status 'baru'
                        'diproses_apoteker' => 'info', // Warna badge untuk status 'diproses_apoteker'
                        'selesai' => 'success', // Warna badge untuk status 'selesai'
                        'dibatalkan' => 'danger', // Warna badge untuk status 'dibatalkan'
                        default => 'gray', // Warna default
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y, H:i')->label('Tanggal Order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y, H:i')->label('Terakhir Diubah')->sortable(),
            ])
            ->filters([
                // Filter berdasarkan Status Order
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'baru' => 'Baru',
                        'diproses_apoteker' => 'Diproses Apoteker',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->label('Filter Status'),

                // Filter berdasarkan Tanggal Order (created_at)
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Action untuk melihat detail order
                Tables\Actions\EditAction::make(), // Action untuk mengedit order
                // Action kustom untuk menyelesaikan order dan mengurangi stok
                Tables\Actions\Action::make('markAsCompleted')
                    ->label('Selesaikan Order')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation() // Membutuhkan konfirmasi pengguna
                    ->hidden(fn (Order $record): bool => $record->status === 'selesai' || $record->status === 'dibatalkan') // Sembunyikan jika sudah selesai/dibatalkan
                    ->action(function (Order $record) {
                        DB::beginTransaction(); // Memulai transaksi database
                        try {
                            // 1. Cek stok sebelum menyelesaikan order
                            foreach ($record->items as $item) {
                                $obat = Obat::find($item->ID_OBAT);
                                if (!$obat || $obat->JUMLAH_STOCK < $item->quantity) {
                                    DB::rollBack(); // Batalkan transaksi jika stok tidak cukup
                                    \Filament\Notifications\Notification::make()
                                        ->title('Gagal Menyelesaikan Order')
                                        ->body('Stok ' . ($obat ? $obat->NAMA_OBAT : 'Obat') . ' tidak mencukupi untuk order #' . $record->id . '.')
                                        ->danger()
                                        ->send();
                                    return; // Hentikan eksekusi action
                                }
                            }

                            // 2. Kurangi stok dan update status
                            foreach ($record->items as $item) {
                                $obat = Obat::find($item->ID_OBAT);
                                if ($obat) { // Pastikan obat ditemukan
                                    $obat->decrement('JUMLAH_STOCK', $item->quantity); // Kurangi stok
                                }
                            }

                            $record->status = 'selesai'; // Ubah status order menjadi 'selesai'
                            $record->save(); // Simpan perubahan order

                            DB::commit(); // Komit transaksi

                            \Filament\Notifications\Notification::make()
                                ->title('Order Selesai!')
                                ->body('Order #' . $record->id . ' berhasil diselesaikan. Stok obat telah diperbarui.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            DB::rollBack(); // Rollback transaksi jika terjadi error
                            \Filament\Notifications\Notification::make()
                                ->title('Terjadi Kesalahan')
                                ->body('Gagal menyelesaikan order: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Mengaktifkan penghapusan massal
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Urutkan berdasarkan tanggal terbaru
    }

    public static function getRelations(): array
    {
        return [
            // Aktifkan ini untuk menampilkan item order di halaman View/Edit order sebagai tab terpisah
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    // Method ini dipanggil sebelum data form utama disimpan saat membuat record baru.
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Isi dengan ID kasir yang sedang login
        $data['status'] = 'baru'; // Status awal order adalah 'baru'

        $total = 0.00; // Inisialisasi total dengan float
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                // Pastikan sub_total diakses dan dikonversi ke float
                $total += (float) ($item['sub_total'] ?? 0.00);
            }
        }
        $data['total_amount'] = $total; // Simpan total akhir

        return $data;
    }

    // Method ini dipanggil sebelum data form utama disimpan saat mengedit record.
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $total = 0.00; // Inisialisasi total dengan float
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                // Pastikan sub_total diakses dan dikonversi ke float
                $total += (float) ($item['sub_total'] ?? 0.00);
            }
        }
        $data['total_amount'] = $total; // Simpan total akhir

        return $data;
    }

    // Mengaktifkan refresh otomatis tabel daftar order ketika status order berubah (misalnya dari 'baru' ke 'selesai')
    protected static function getLivewireListeningBindings(): array
    {
        return [
            // Mendengarkan event 'status.updated' pada channel 'filament.orders'
            'echo:filament.orders,status.updated' => '$refresh',
        ];
    }
}