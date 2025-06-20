<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Tambahkan use statements untuk komponen Form dan Table yang akan digunakan
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    // Menggunakan ID_SUPPLIER sebagai slug/identifier di URL, jika tidak ingin 'id'
    // protected static ?string $recordTitleAttribute = 'NAMA_SUPPLIER'; // Opsional, untuk judul record
    // protected static ?string $slug = 'suppliers'; // Opsional, untuk URL

    protected static ?string $navigationIcon = 'heroicon-o-truck'; // Ganti ikon jika mau
    protected static ?string $navigationLabel = 'Data Supplier';
    protected static ?string $pluralModelLabel = 'Supplier'; // Nama jamak untuk model
    protected static ?string $modelLabel = 'Supplier'; // Nama tunggal


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('NAMA_SUPPLIER')
                    ->required()
                    ->maxLength(100)
                    ->label('Nama Supplier'), // Label kustom
                Textarea::make('ALAMAT_SUPPLIER')
                    ->label('Alamat Supplier')
                    ->columnSpanFull(), // Agar field ini mengambil lebar penuh
                TextInput::make('TELEPON_SUPPLIER')
                    ->maxLength(20)
                    ->tel() // Menunjukkan ini field telepon
                    ->label('Telepon Supplier'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NAMA_SUPPLIER')
                    ->searchable() // Mengaktifkan pencarian pada kolom ini
                    ->sortable()   // Mengaktifkan pengurutan
                    ->label('Nama Supplier'),
                TextColumn::make('TELEPON_SUPPLIER')
                    ->label('Telepon'),
                TextColumn::make('ALAMAT_SUPPLIER')
                    ->label('Alamat')
                    ->limit(50) // Batasi panjang teks yang ditampilkan di tabel
                    ->tooltip(fn (Supplier $record): string => $record->ALAMAT_SUPPLIER ?? ''), // Tooltip untuk teks penuh
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true) // Bisa disembunyikan/ditampilkan
                    ->label('Tanggal Dibuat'),
            ])
            ->filters([
                // Tambahkan filter jika perlu
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan action view
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ObatsRelationManager::class, // Jika ingin menampilkan daftar obat terkait di halaman supplier
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
            'view' => Pages\ViewSupplier::route('/{record}'), // Tambahkan route view
        ];
    }    
}