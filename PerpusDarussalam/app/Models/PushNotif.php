<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotif extends Model
{
    use HasFactory;

    protected $table = 'push_notif';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
    ];
}
