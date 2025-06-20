<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('kasir')->after('email'); // Default role adalah 'kasir'
        });
    }
    // Method down() untuk menghapus kolom role juga sudah ada di contoh sebelumnya, pastikan itu juga benar.
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
