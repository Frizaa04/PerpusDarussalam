<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    public function borrowing(){
        return $this->belongsTo(Borrowing::class);
    }
}
