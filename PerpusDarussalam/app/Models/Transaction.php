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
    
    public function getJenisLabelAttribute()
    {
        return match ($this->jenis) {
            'pembuatan_kartu'    => 'Pembuatan Kartu',
            'kehilangan_kartu'   => 'Kehilangan Kartu',
            'denda_keterlambatan'=> 'Denda Keterlambatan',
            default              => ucwords(str_replace('_', ' ', $this->jenis ?? '-')),
        };
    }

    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal ?? 0, 0, ',', '.');
    }
}