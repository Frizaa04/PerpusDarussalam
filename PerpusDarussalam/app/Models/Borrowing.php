<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function Book(){
        return $this->belongTo(Book::class);
    }

}
