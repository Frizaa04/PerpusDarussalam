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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nis')->nullable(); // Dibuat nullable jika Umum/Guru tidak memiliki NIS
        $table->string('nik')->nullable();
        $table->string('nip')->nullable();
        $table->string('name');
        $table->string('email')->unique(); // Tambahan email unik
        $table->string('password'); // Tambahan password untuk autentikasi
        $table->enum('role', ['siswa', 'guru', 'umum'])->default('siswa'); // Mengubah role menjadi 3 jenis
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); // Tambahan jenis kelamin (Laki-laki / Perempuan)
        $table->string('alamat')->nullable();
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};