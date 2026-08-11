<?php
// database/migrations/xxxx_xx_xx_add_borrowing_id_to_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('borrowing_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('borrowings')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['borrowing_id']);
            $table->dropColumn('borrowing_id');
        });
    }
};