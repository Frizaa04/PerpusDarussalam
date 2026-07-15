<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class visits extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $casts =[
        'visited_at' => 'datetime',
    ];
}
