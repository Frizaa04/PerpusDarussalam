<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'nisn', 
        'nik', 
        'status', 
        'role', 
        'jenis_kelamin', 
        'alamat', 
        'foto', 
        'jenjang', 
        'kelas', 
        'masa_berlaku_mulai',
        'masa_berlaku_sampai'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function borrowings(){
        return $this->hasMany(Borrowing::class);
    }

    public function visits(){
        return $this->hasMany(visits::class);
    }

    public function bookLogs(){
        return $this->hasMany(BookLogs::class);
    }

    public function getStatusKartuAttribute($value)
    {
        // Jika tanggal hari ini melebihi 'masa_berlaku_sampai', maka anggap expired
        if ($this->masa_berlaku_sampai && Carbon::now()->greaterThan(Carbon::parse($this->masa_berlaku_sampai))) {
            return 'expired';
        }
        
        return 'aktif';
    }
}