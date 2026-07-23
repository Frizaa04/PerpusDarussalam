<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'kode_buku',
        'categories_id',
        'judul',
        'penulis',
        'penerbit',
        'isbn',
        'tanggal_pembelian',
        'tahun_terbit',
        'stok',
        'deskripsi',
        'rak',
        'cover',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function logs()
    {
        return $this->hasMany(BookLogs::class);
    }

    // Tambahkan relasi ini agar terhubung ke tabel book_items
    public function bookItems()
    {
        return $this->hasMany(BookItem::class, 'book_id');
    }
}