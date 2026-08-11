<?php
// database/migrations/xxxx_xx_xx_add_kehilangan_buku_to_transactions_jenis_enum.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN jenis ENUM(
            'pembuatan_kartu',
            'kehilangan_kartu',
            'denda_keterlambatan',
            'kehilangan_buku'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN jenis ENUM(
            'pembuatan_kartu',
            'kehilangan_kartu',
            'denda_keterlambatan'
        ) NOT NULL");
    }
};