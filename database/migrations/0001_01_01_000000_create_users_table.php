<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nisn')->nullable()->index();
            $table->string('nik')->nullable()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['siswa', 'guru'])
                ->default('siswa')
                ->index();
            $table->enum('role', ['admin','user'])
                ->default('user');
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->enum('jenjang', ['MA','MTS'])
                ->nullable()
                ->index();
            $table->string('kelas')->nullable();
            $table->date('masa_berlaku_mulai')->nullable();
            $table->date('masa_berlaku_sampai')->nullable();
            $table->enum('status_kartu', ['aktif','expired'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};