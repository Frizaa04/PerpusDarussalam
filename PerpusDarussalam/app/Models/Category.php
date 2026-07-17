<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi'
    ];

    public function books(){
        return $this->hasMany(Book::class);
    }
}
