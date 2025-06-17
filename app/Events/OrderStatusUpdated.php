<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): array
    {
        // Kita bisa broadcast ke channel yang sama dengan notifikasi order baru,
        // atau channel yang berbeda. Mari kita gunakan channel yang sama untuk kesederhanaan.
        return [
            new Channel('apoteker-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        // Beri nama yang berbeda agar bisa dibedakan dari event 'order.baru'
        return 'order.status.updated';
    }

    // Kita bisa kirim data order yang diupdate jika perlu
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'new_status' => $this->order->status,
        ];
    }
}