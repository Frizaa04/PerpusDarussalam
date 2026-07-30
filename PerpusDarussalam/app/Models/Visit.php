<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visits'; // Menyesuaikan dengan nama tabel di database

    protected $fillable = [
        'user_id',
        'visited_at',
    ];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}