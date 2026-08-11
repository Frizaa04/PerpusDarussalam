<?php
// app/Models/Tarif.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = ['jenis', 'nominal', 'keterangan'];
}