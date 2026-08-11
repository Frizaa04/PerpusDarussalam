<?php
// database/migrations/xxxx_xx_xx_add_status_bayar_to_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status_bayar', ['belum_bayar', 'sudah_bayar'])
                  ->default('belum_bayar')
                  ->after('nominal');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status_bayar');
        });
    }
};