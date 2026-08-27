<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'borrowing_id', 
        'title', 
        'message', 
        'status', 
        'type', 
        'read_at'
    ];
    public function borrowing(){
        return $this->belongsTo(Borrowing::class);
    }
}
