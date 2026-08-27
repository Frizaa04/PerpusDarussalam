<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookItem extends Model
{
    protected $fillable = [
        'book_id',
        'nomor_inventaris',
        'kondisi',
        'status_pinjam',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'book_item_id');
    }
}