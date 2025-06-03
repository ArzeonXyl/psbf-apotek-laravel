<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // <-- PENTING: Import facade Log

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order; // Kita akan mengirim objek Order yang di-update

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     * Kita bisa menggunakan channel publik atau private.
     * Untuk Filament, channel publik atau private yang bisa diakses admin sudah cukup.
     * Jika Anda hanya ingin admin yang melihat, gunakan PrivateChannel.
     * Untuk kesederhanaan, mari gunakan Channel biasa.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Channel yang akan didengarkan oleh Filament di dashboard admin.
        // Bisa juga `new PrivateChannel('filament.admin.orders')`
        return [
            new Channel('filament.orders'), // Nama channel baru untuk update order
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.updated'; // Nama event untuk frontend
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $payload = [
            'order_id' => $this->order->id,
            'new_status' => $this->order->status,
            // Anda bisa menyertakan data lain yang relevan jika dibutuhkan oleh Filament
            // misal: 'total_amount' => $this->order->total_amount,
            // 'customer_name' => $this->order->customer_name,
        ];

        // --- TAMBAHKAN LOG INI ---
        Log::info('Preparing to broadcast OrderStatusUpdated event with payload:', $payload);
        // --- AKHIR LOG TAMBAHAN ---

        return $payload;
    }
}