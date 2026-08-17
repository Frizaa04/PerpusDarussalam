<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    // Agar bisa tambah data massal 
    protected $fillable = [
        'user_id',
        'borrowing_id',
        'jenis',
        'nominal',
        'keterangan',
        'tanggal',
        'status_bayar'
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function getJenisLabelAttribute()
    {
        return match ($this->jenis) {
            'pembuatan_kartu'    => 'Pembuatan Kartu',
            'kehilangan_kartu'   => 'Kehilangan Kartu',
            'denda_keterlambatan'=> 'Denda Keterlambatan',
            'perpanjang_kartu'   => 'Perpanjang Kartu',
            'kehilangan_buku'    => 'Kehilangan Buku',
            default              => ucwords(str_replace('_', ' ', $this->jenis ?? '-')),
        };
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal ?? 0, 0, ',', '.');
    }
}