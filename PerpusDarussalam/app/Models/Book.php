<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public function borrowings(){
        return $this->hasMany(Borrowing::class);    
    }

    public function categories(){
        return $this->belongsTo(Category::class);
    }

    public function logs(){
    return $this->hasMany(BookLogs::class);
    }
}
