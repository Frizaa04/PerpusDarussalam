<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'nama' => 'Referensi Guru/Siswa',
            'deskripsi' => 'Buku Referensi untuk Guru dan Siswa.']);
        Category::create([
            'nama' => 'Buku Bacaan',
                'deskripsi' => 'Buku Bacaan.']);
    }
}
