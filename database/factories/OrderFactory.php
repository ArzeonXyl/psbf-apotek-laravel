<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Obat;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil user acak yang perannya kasir atau admin
        $kasir = User::whereIn('role', ['admin', 'kasir'])->inRandomOrder()->first();
        
        // Daftar kemungkinan status untuk order
        $statuses = ['baru', 'selesai', 'dibatalkan', 'diproses_apoteker'];

        return [
            'user_id' => $kasir->id,
            'customer_name' => fake()->name(),
            'total_amount' => 0, // Nilai awal, akan diupdate setelah item dibuat
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->boolean(25) ? fake()->sentence() : null, // 25% kemungkinan ada catatan
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'), // Transaksi dalam 1 tahun terakhir
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Mengkonfigurasi factory untuk melakukan aksi setelah sebuah Order dibuat.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            // Setelah satu Order dibuat, buatkan beberapa OrderItem untuknya.
            // Kita akan buat antara 1 sampai 4 item obat per order.
            $items = OrderItem::factory(rand(1, 4))->make([
                'order_id' => $order->id,
            ]);

            $total = 0;
            foreach ($items as $item) {
                // Simpan setiap item ke database
                $item->save();
                // Jumlahkan subtotalnya
                $total += $item->sub_total;
            }

            // Update total_amount pada Order induknya
            $order->total_amount = $total;
            $order->save();

            // Jika status order yang dibuat adalah 'selesai', kita langsung kurangi stoknya
            // untuk mensimulasikan alur yang sudah lengkap.
            if ($order->status === 'selesai') {
                foreach ($order->items as $item) {
                    // Gunakan "decrement" yang aman
                    $item->obat?->decrement('JUMLAH_STOCK', $item->quantity);
                }
            }
        });
    }
}