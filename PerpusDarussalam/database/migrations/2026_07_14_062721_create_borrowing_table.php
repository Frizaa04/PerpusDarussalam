<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (peminjam)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
                  
            $table->foreignId('book_item_id')
                  ->constrained('book_items')
                  ->cascadeOnDelete();
                  
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            
            $table->enum('status', [
                'dipinjam',
                'dikembalikan',
                'terlambat'
            ])->default('dipinjam');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Perbaiki juga nama tabel saat rollback
        Schema::dropIfExists('borrowings');
    }
};