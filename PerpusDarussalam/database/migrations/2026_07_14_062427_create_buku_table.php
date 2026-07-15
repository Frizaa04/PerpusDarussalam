<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->foreignId('categories_id')
            ->constrained()
            ->cascadeOnDelete();
        $table->string('kode_buku')->unique();
        $table->string('judul');
        $table->string('penulis');
        $table->string('penerbit');
        $table->year('tahun_terbit');
        $table->integer('stok')->default(0);
        $table->string('cover')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
