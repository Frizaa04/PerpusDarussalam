<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visits extends Model
{
    // TAMBAHKAN BARIS INI
    protected $fillable = [
        'user_id',
        'visited_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $casts =[
        'visited_at' => 'datetime',
    ];
}