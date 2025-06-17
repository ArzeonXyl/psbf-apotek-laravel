<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    // Pastikan nama relasi ini cocok dengan metode `items()` di model Order Anda
    protected static string $relationship = 'items'; 

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ID_OBAT')
                    ->label('Nama Obat')
                    ->options(\App\Models\Obat::pluck('NAMA_OBAT', 'ID_OBAT'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $obat = \App\Models\Obat::find($state);
                        if ($obat) {
                            $set('price_at_purchase', $obat->HARGA);
                            $quantity = $this->get('quantity') ?? 1;
                            $set('sub_total', $obat->HARGA * $quantity);
                        } else {
                            $set('price_at_purchase', 0);
                            $set('sub_total', 0);
                        }
                    })
                    ->columnSpan(1),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $price = $get('price_at_purchase');
                        if ($price && $state) {
                            $set('sub_total', $price * $state);
                        } else {
                            $set('sub_total', 0);
                        }
                    })
                    ->columnSpan(1),

                Forms\Components\TextInput::make('price_at_purchase')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('sub_total')
                    ->label('Sub Total')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(1),
            ])->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ID_OBAT') 
            ->columns([
                Tables\Columns\TextColumn::make('obat.NAMA_OBAT')->label('Nama Obat'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah'),
                Tables\Columns\TextColumn::make('price_at_purchase')->money('IDR', locale:'id')->label('Harga Satuan'),
                Tables\Columns\TextColumn::make('sub_total')->money('IDR', locale:'id')->label('Sub Total'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}