<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    // Agar bisa tambah data massal nanti
    protected $fillable = [
        'user_id',
        'jenis',
        'nominal',
        'keterangan',
        'tanggal',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}