<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('order_id')->comment('ID Order induk')->constrained('orders')->onDelete('cascade'); // FK ke tabel orders
            $table->foreignId('ID_OBAT')->comment('ID Obat yang dibeli')->constrained('obat', 'ID_OBAT')->onDelete('cascade'); // FK ke tabel obat
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 15, 2)->comment('Harga obat per unit saat transaksi');
            $table->decimal('sub_total', 15, 2)->comment('Total harga untuk item ini (quantity * price_at_purchase)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};