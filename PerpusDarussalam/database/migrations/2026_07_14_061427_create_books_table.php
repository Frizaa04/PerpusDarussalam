<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categories_id')
                ->constrained('categories')
                ->cascadeOnDelete(); 
            $table->string('kode_buku')->unique(); 
            $table->string('judul')->index(); 
            $table->string('penulis')->index(); 
            $table->string('penerbit');
            $table->year('tahun_terbit')->nullable();
            $table->string('isbn')->index(); 
            $table->date('tanggal_pembelian');
            $table->integer('stok')->default(0);
            $table->string('cover')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('rak')->index(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};