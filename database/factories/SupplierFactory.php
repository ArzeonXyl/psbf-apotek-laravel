<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Menggunakan library Faker bawaan Laravel untuk data acak
        return [
            'NAMA_SUPPLIER' => 'PT ' . fake()->company(), // Contoh: PT Medika Jaya
            'ALAMAT_SUPPLIER' => fake()->address(), // Alamat acak
            'TELEPON_SUPPLIER' => fake()->e164PhoneNumber(), // Nomor telepon format internasional
        ];
    }
}