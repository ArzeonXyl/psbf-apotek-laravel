// database/migrations/XXXX_XX_XX_XXXXXX_remove_customer_phone_from_orders_table.php

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
            // Drop kolom 'customer_phone' jika sudah ada
            $table->dropColumn('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kembali kolom jika ingin rollback
            $table->string('customer_phone')->nullable()->after('customer_name');
        });
    }
};