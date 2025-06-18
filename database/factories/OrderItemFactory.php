<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Obat; // Import model Obat

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil satu obat acak yang stoknya ada
        $obat = Obat::where('JUMLAH_STOCK', '>', 0)->inRandomOrder()->first();

        // Jika tidak ada obat yang stoknya ada, fallback ke obat acak mana pun
        if (!$obat) {
            $obat = Obat::inRandomOrder()->first();
        }

        // Jika tidak ada obat sama sekali di database, factory tidak bisa berjalan
        if (!$obat) {
            return [];
        }
        
        $quantity = fake()->numberBetween(1, 3); // Beli antara 1-3 unit
        $price = $obat->HARGA;
        $sub_total = $price * $quantity;

        return [
            'ID_OBAT' => $obat->ID_OBAT,
            'quantity' => $quantity,
            'price_at_purchase' => $price,
            'sub_total' => $sub_total,
        ];
    }
}