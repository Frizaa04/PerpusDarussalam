<?php
// database/migrations/xxxx_xx_xx_create_tarifs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pembuatan_kartu', 'kehilangan_kartu', 'denda_keterlambatan', 'kehilangan_buku', 'perpanjang_kartu'])->unique();
            $table->integer('nominal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Seed nilai default supaya langsung bisa dipakai
        DB::table('tarifs')->insert([
            ['jenis' => 'pembuatan_kartu', 'nominal' => 10000, 'keterangan' => 'Biaya cetak kartu baru', 'created_at' => now(), 'updated_at' => now()],
            ['jenis' => 'kehilangan_kartu', 'nominal' => 15000, 'keterangan' => 'Biaya cetak ulang kartu hilang', 'created_at' => now(), 'updated_at' => now()],
            ['jenis' => 'denda_keterlambatan', 'nominal' => 1000, 'keterangan' => 'Denda per hari keterlambatan', 'created_at' => now(), 'updated_at' => now()],
            ['jenis' => 'kehilangan_buku', 'nominal' => 0, 'keterangan' => 'Ganti rugi buku hilang (nominal disesuaikan manual per buku)', 'created_at' => now(), 'updated_at' => now()],
            ['jenis' => 'perpanjang_kartu', 'nominal' => 5000, 'keterangan' => 'Biaya perpanjang kartu', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};