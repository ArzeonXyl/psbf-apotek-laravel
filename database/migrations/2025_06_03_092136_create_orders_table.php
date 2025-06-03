<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment (akan menjadi 'id' secara default)
            $table->foreignId('user_id')->nullable()->comment('ID Kasir/User yang membuat order')->constrained('users')->onDelete('set null');
            $table->string('customer_name')->nullable()->comment('Nama Pelanggan');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('Total harga keseluruhan order');
            $table->string('status')->default('baru')->comment('Status order: baru, diproses_apoteker, selesai, dibatalkan');
            $table->text('notes')->nullable()->comment('Catatan tambahan untuk order');
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};