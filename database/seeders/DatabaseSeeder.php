<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Import model-model yang akan kita gunakan
use App\Models\User;
use App\Models\Supplier;
use App\Models\Obat;
use App\Models\Order;
use App\Models\OrderItem;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat user default (Admin, Kasir, Apoteker)
        // Ini lebih baik daripada membuatnya via Tinker setiap kali reset database
        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@apotek.test',
        //     'role' => 'admin',
        //     'password' => bcrypt('admin123'), // Ganti dengan password aman
        // ]);
        // User::factory()->create([
        //     'name' => 'Kasir User',
        //     'email' => 'kasir@apotek.test',
        //     'role' => 'kasir',
        //     'password' => bcrypt('kasir123'), // Ganti dengan password aman
        // ]);
        // User::factory()->create([
        //     'name' => 'Apoteker User',
        //     'email' => 'apoteker@apotek.test',
        //     'role' => 'apoteker',
        //     'password' => bcrypt('apoteker123'), // Ganti dengan password aman
        // ]);


        // 2. Buat 15 data Supplier dummy
        // Factory akan dijalankan 15 kali dan datanya dimasukkan ke tabel 'supplier'
        // Supplier::factory(15)->create();
        
        // 3. Buat 100 data Obat dummy
        // Factory akan dijalankan 100 kali dan datanya dimasukkan ke tabel 'obat'
        // Ini hanya akan berhasil jika sudah ada data di tabel 'supplier'
        // Obat::factory(100)->create();

        // Anda bisa menambahkan pemanggilan seeder lain di sini jika ada
        // $this->call([
        //     OrderSeeder::class,
        // ]);
        Order::factory(50)->create();
    }
}