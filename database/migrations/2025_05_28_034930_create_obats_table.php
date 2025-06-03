<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) { // Nama tabel 'obat'
            $table->id('ID_OBAT'); // INT NOT NULL AUTO_INCREMENT, PRIMARY KEY
            $table->string('NAMA_OBAT', 50);
            $table->string('KATEGORI', 100)->nullable(); // Sesuai SQL Anda, KATEGORI bisa NULL
            $table->text('KETERANGAN'); // VARCHAR(10000) lebih cocok jadi TEXT
            $table->integer('JUMLAH_STOCK')->default(0);
            $table->decimal('HARGA', 10, 2);
            $table->date('EXP')->nullable(); // Sesuai SQL Anda, EXP bisa NULL

            // Foreign Key untuk ID_SUPPLIER
            $table->foreignId('ID_SUPPLIER')
                  ->nullable() // Bisa NULL
                  ->constrained('supplier', 'ID_SUPPLIER') // Mereferensi ID_SUPPLIER di tabel 'supplier'
                  ->onDelete('set null'); // Jika supplier dihapus, ID_SUPPLIER di obat jadi NULL

            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};