<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier; // Import model Supplier

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Obat>
 */
class ObatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Beberapa contoh kategori obat
        $categories = ['Analgesik', 'Antibiotik', 'Suplemen & Vitamin', 'Obat Batuk & Pilek', 'Obat Saluran Pencernaan', 'Obat Kulit'];
        
        return [
            'NAMA_OBAT' => 'Obat ' . fake()->word() . ' ' . fake()->randomElement(['500mg', '250mg', 'Sirup', 'Tablet']),
            'KATEGORI' => fake()->randomElement($categories),
            'KETERANGAN' => fake()->paragraph(3), // Keterangan dengan 3 paragraf acak
            'JUMLAH_STOCK' => fake()->numberBetween(0, 500), // Stok acak antara 0 - 500
            'HARGA' => fake()->numberBetween(5000, 150000), // Harga acak antara 5rb - 150rb
            'EXP' => fake()->dateTimeBetween('+6 months', '+3 years')->format('Y-m-d'), // Tanggal kadaluarsa 6 bln - 3 thn dari sekarang
            'ID_SUPPLIER' => Supplier::query()->inRandomOrder()->first()?->ID_SUPPLIER, // Ambil ID supplier acak yang sudah ada
        ];
    }
}