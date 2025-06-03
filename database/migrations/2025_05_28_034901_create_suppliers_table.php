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
        Schema::create('supplier', function (Blueprint $table) { // Nama tabel 'supplier' sesuai referensi FK Anda
            $table->id('ID_SUPPLIER'); // Primary key INT AUTO_INCREMENT dengan nama ID_SUPPLIER
            $table->string('NAMA_SUPPLIER', 100);
            $table->text('ALAMAT_SUPPLIER')->nullable();
            $table->string('TELEPON_SUPPLIER', 20)->nullable();
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};