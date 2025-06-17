<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getListeners(): array
    {
        return [
            // PERBAIKAN: Tambahkan titik di depan nama event
            'echo:apoteker-channel,.order.baru' => '$refresh',
            'echo:apoteker-channel,.order.status.updated' => '$refresh',
        ];
    }
}
