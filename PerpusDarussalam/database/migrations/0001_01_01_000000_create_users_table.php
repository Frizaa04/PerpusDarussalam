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
        $table->string('nis')->nullable();
        $table->string('nik')->nullable();
        $table->string('nip')->nullable();
        $table->string('name');
        $table->string('email')->unique(); 
        $table->string('password'); 
        $table->enum('role', ['siswa', 'guru', 'umum'])->default('siswa'); 
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); 
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