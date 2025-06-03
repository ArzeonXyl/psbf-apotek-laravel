<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Obat; // <-- IMPORT MODEL OBAT
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get; // Untuk mendapatkan value field lain secara reaktif
use Filament\Forms\Set; // Untuk mengisi value field lain secara reaktif
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan ID user yang login

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
                    ->maxLength(255),

                Repeater::make('items') // Ini akan berelasi dengan method 'items()' di model Order
                    ->relationship() // Memberitahu Repeater untuk mengelola relasi
                    ->label('Item Obat')
                    ->schema([
                        Select::make('ID_OBAT')
                            ->label('Nama Obat')
                            ->options(Obat::query()->pluck('NAMA_OBAT', 'ID_OBAT')) // Ambil opsi dari model Obat
                            ->searchable()
                            ->required()
                            ->reactive() // Penting untuk update harga otomatis
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $obat = Obat::find($state);
                                if ($obat) {
                                    $set('price_at_purchase', $obat->HARGA);
                                    // Hitung subtotal jika quantity sudah ada
                                    $quantity = $get('quantity') ?? 1;
                                    $set('sub_total', $obat->HARGA * $quantity);
                                } else {
                                    $set('price_at_purchase', 0);
                                    $set('sub_total', 0);
                                }
                            }),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $price = $get('price_at_purchase');
                                if ($price && $state) {
                                    $set('sub_total', $price * $state);
                                } else {
                                    $set('sub_total', 0);
                                }
                            }),
                        TextInput::make('price_at_purchase')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // Harga diambil otomatis, jadi disabled
                            ->dehydrated(), // Agar tetap tersimpan meskipun disabled
                        TextInput::make('sub_total')
                            ->label('Sub Total')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->addActionLabel('Tambah Obat')
                    ->columns(4) // Atur layout repeater
                    ->defaultItems(1) // Default ada 1 item saat form dibuka
                    ->collapsible()
                    ->cloneable()
                    ->reorderable(false), // Matikan reorder jika tidak perlu

                // Placeholder untuk Total Keseluruhan (akan dihitung di backend saat save)
                // atau bisa dibuat reaktif dengan lebih banyak Livewire magic.
                // Untuk simple, kita hitung saat save.
                Placeholder::make('total_amount_display')
                    ->label('Total Sementara (akan dihitung final saat simpan)')
                    ->content(function (Get $get): string {
                        $total = 0;
                        foreach ($get('items') as $item) {
                            $total += $item['sub_total'] ?? 0;
                        }
                        return 'Rp ' . number_format($total, 0, ',', '.');
                    })
                    ->reactive(), // Agar placeholder ini juga ikut update

                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),

                // Field tersembunyi untuk status dan user_id
                // Akan diisi otomatis saat proses penyimpanan
            ])->columns(1); // Atur layout form utama
    }

    public static function table(Table $table): Table
    {
        // Untuk tabel daftar order, bisa kita buat lebih informatif
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID Order')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Nama Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Kasir')->sortable(), // Menampilkan nama kasir via relasi
                Tables\Columns\TextColumn::make('total_amount')->money('IDR', locale:'id')->label('Total')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->searchable()->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'warning',
                        'diproses_apoteker' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y, H:i')->label('Tanggal Order')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Kita butuh halaman ViewOrder nanti
                Tables\Actions\EditAction::make(), // Mungkin hanya status tertentu yang boleh diedit
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Urutkan berdasarkan terbaru
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ItemsRelationManager::class, // Jika ingin detail item di halaman edit order
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'), // Siapkan untuk halaman view
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    // Method ini akan dipanggil sebelum data form utama disimpan ke tabel 'orders'
    // Kita gunakan untuk mengisi user_id, status awal, dan menghitung total_amount
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Isi dengan ID kasir yang sedang login
        $data['status'] = 'baru'; // Status awal order

        // Hitung total_amount dari sub_total semua item
        $total = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $total += $item['sub_total'] ?? 0;
            }
        }
        $data['total_amount'] = $total;

        return $data;
    }

    // Jika Anda mengizinkan edit, Anda mungkin juga butuh mutateFormDataBeforeSave
    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Hitung ulang total_amount jika item diubah saat edit
        $total = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $total += $item['sub_total'] ?? 0;
            }
        }
        $data['total_amount'] = $total;
        // Anda mungkin tidak ingin status atau user_id diubah saat edit oleh kasir biasa
        // unset($data['user_id']); 
        // unset($data['status']);
        return $data;
    }
}