<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Events\NewOrderCreated; // <-- 1. IMPORT EVENT YANG SUDAH KITA BUAT

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Hook ini akan dijalankan secara otomatis oleh Filament
     * setelah record (dalam hal ini, Order baru) berhasil dibuat dan disimpan ke database.
     */
    protected function afterCreate(): void
    {
        // '$this->record' akan berisi instance dari model Order yang baru saja dibuat.
        if ($this->record) {
            // Memicu event NewOrderCreated dengan membawa data order yang baru dibuat.
            NewOrderCreated::dispatch($this->record); 
        }
    }

    /**
     * Mengarahkan pengguna kembali ke halaman daftar order (index) 
     * setelah berhasil membuat order baru.
     * Ini adalah perilaku default, tapi bisa dikustomisasi jika perlu.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * (Opsional) Kustomisasi notifikasi sukses dari Filament setelah membuat record.
     * Jika tidak didefinisikan, akan menggunakan notifikasi default Filament.
     */
    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Order baru berhasil ditambahkan!';
    // }
}