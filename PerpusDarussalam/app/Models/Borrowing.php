<?php

namespace App\Models;

use App\Models\notification as ModelsNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

class Borrowing extends Model
{
    public function User(){
        return $this->belongsTo(User::class);
    }
    public function Book(){
        return $this->belongTo(Book::class);
    }
    public function Notifications(){
        return $this->hasMany(ModelsNotification::class);
    }

}
