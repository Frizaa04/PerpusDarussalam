<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visits extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $casts =[
        'visited_at' => 'datetime',
    ];
}
