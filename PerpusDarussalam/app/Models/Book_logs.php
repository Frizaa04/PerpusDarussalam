<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book_logs extends Model
{
    public function book(){
        return $this->belongsTo(Book::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
