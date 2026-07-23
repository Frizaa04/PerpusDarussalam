<?php

namespace App\Models;

use App\Models\notification as ModelsNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_item_id', // Diubah dari book_id menjadi book_item_id
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ubah relasi dari book() ke bookItem()
    public function bookItem()
    {
        return $this->belongsTo(BookItem::class, 'book_item_id');
    }

    public function notifications()
    {
        return $this->hasMany(ModelsNotification::class);
    }
}