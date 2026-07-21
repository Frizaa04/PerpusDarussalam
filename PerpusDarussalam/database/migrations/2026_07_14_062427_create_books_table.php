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
        Schema::create('Books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categories_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->string('kode_buku')->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun_terbit');
            $table->string('isbn'); 
            $table->date('tanggal_pembelian');
            $table->integer('stok')->default(0);
            $table->string('cover')->nullable();
            $table->string('deskripsi');
            $table->string('rak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
