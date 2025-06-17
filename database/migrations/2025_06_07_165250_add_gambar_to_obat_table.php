// database/migrations/XXXX_XX_XX_XXXXXX_add_gambar_to_obat_table.php

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
        Schema::table('obat', function (Blueprint $table) {
            // Menambahkan kolom 'gambar' setelah kolom 'EXP'
            $table->string('gambar')->nullable()->after('EXP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};