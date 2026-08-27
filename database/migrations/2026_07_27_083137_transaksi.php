<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->onDelete('set null');
            
            $table->enum('jenis', [
                'pembuatan_kartu', 
                'kehilangan_kartu', 
                'denda_keterlambatan', 
                'kehilangan_buku',
                'perpanjang_kartu'
            ]);
            
            $table->integer('nominal');
            
            $table->enum('status_bayar', ['belum_bayar', 'sudah_bayar'])
                  ->default('belum_bayar');
                  
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};