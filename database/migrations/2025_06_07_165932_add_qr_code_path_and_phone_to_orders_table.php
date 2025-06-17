// database/migrations/XXXX_XX_XX_XXXXXX_add_qr_code_path_and_phone_to_orders_table.php

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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('qr_code_path')->nullable()->after('notes'); // Sesuaikan posisi jika 'notes' ada
            // Jika kolom 'notes' belum ada, tambahkan baris ini juga:
            // $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_phone');
            $table->dropColumn('qr_code_path');
            // Jika Anda menambahkan 'notes' di up(), drop juga di sini:
            // $table->dropColumn('notes');
        });
    }
};