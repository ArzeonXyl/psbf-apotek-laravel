<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObatResource\Pages;
use App\Filament\Resources\ObatResource\RelationManagers;
use App\Models\Obat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObatResource extends Resource
{
    protected static ?string $model = Obat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('NAMA_OBAT')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('KATEGORI')
                    ->maxLength(100),
                Forms\Components\Textarea::make('KETERANGAN')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('JUMLAH_STOCK')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('HARGA')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('EXP'),
                Forms\Components\TextInput::make('ID_SUPPLIER')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('NAMA_OBAT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('KATEGORI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('JUMLAH_STOCK')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('HARGA')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('EXP')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ID_SUPPLIER')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObats::route('/'),
            'create' => Pages\CreateObat::route('/create'),
            'edit' => Pages\EditObat::route('/{record}/edit'),
        ];
    }
}
