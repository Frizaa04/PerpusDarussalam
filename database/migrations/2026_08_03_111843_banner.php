<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // Menyimpan path file gambar
            $table->string('title')->nullable(); // Judul opsional
            $table->integer('order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        //
    }
};
