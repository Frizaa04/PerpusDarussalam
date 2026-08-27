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
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('book_item_id')
                  ->constrained('book_items')
                  ->cascadeOnDelete();
                  
            $table->date('tanggal_pinjam')->index(); 
            $table->date('tanggal_jatuh_tempo')->index(); 
            $table->date('tanggal_kembali')->nullable()->index(); 
            
            $table->enum('status', [
                'dipinjam',
                'dikembalikan',
                'terlambat',
                'hilang'
            ])->default('dipinjam')->index();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};