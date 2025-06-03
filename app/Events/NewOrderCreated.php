<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        // Memuat relasi yang dibutuhkan agar data lengkap saat di-broadcast
        $this->order = $order->load(['items.obat', 'user']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('apoteker-channel'), // Channel publik untuk notifikasi Apoteker
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.baru'; // Nama event yang akan didengarkan Echo
    }

    // broadcastWith() tidak wajib jika properti publik $order sudah cukup.
    // Jika ingin kustomisasi payload, Anda bisa gunakan broadcastWith().
    // Secara default, semua properti publik akan di-broadcast.
    // public function broadcastWith(): array
    // {
    //     return ['order' => $this->order->toArray()];
    // }
}