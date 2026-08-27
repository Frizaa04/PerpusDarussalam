<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'categories_id' => 1,
            'kode_buku' => 'BK-001',
            'judul' => 'Pemrograman PHP',
            'penulis' => 'John Doe',
            'penerbit' => 'PT Tech Publishing',
            'isbn' => '978-0-123456-78-9',
            'tahun_terbit' => 2020,
            'tanggal_pembelian' => '2020-01-01',
            'stok' => 10,
            'cover' => null,
            'deskripsi' => 'Buku tentang pemrograman PHP',
            'rak' => 'Rak 1'
        ]);
    }
}
