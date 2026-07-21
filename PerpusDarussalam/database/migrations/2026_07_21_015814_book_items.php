<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            
            // Menghubungkan ke tabel books id
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->cascadeOnDelete();
                  
            // Nomor inventaris unik untuk tiap eksemplar fisik buku
            $table->string('nomor_inventaris')->unique(); 
            
            // Status kondisi dan ketersediaan fisik buku
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status_pinjam', ['tersedia', 'dipinjam'])->default('tersedia');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_items');
    }
};