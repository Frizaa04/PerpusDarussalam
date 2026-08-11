<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE book_items MODIFY COLUMN status_pinjam ENUM(
            'tersedia',
            'dipinjam',
            'hilang'
        ) NOT NULL DEFAULT 'tersedia'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE book_items MODIFY COLUMN status_pinjam ENUM(
            'tersedia',
            'dipinjam'
        ) NOT NULL DEFAULT 'tersedia'");
    }
};