<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function borrowings(){
        return $this->hasMany(Borrowing::class);    
    }

    public function categories(){
        return $this->belongsTo(categories::class);
    }

    public function logs(){
    return $this->hasMany(Book_logs::class);
    }
}
