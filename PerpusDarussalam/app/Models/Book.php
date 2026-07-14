<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function borrowings(){
        return $this->hasMany(Borrowing::class);    
    }

    public function category(){
        return $this->belongsTo(category::class);
    }
}
