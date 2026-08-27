<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nis'           => '2411102441211',
            'name'          => 'Febri Hamzah Jemikan Nata',
            'email'         => 'febri@example.com', 
            'password'      => 'password123',       
            'role'          => 'siswa',             
            'jenis_kelamin' => 'L',                 
            'alamat'        => 'Jl. Merdeka No. 10, Bontang',
            'foto'      => 'default.pdf',
        ]);

        User::create([
            'nis'           => '2411102441212',
            'name'          => 'Muhamad Aditya Nugroho',
            'email'         => 'aditya@example.com',
            'password'      => 'password123',
            'role'          => 'siswa',
            'jenis_kelamin' => 'L',
            'alamat'        => 'Jl. Pahlawan No. 5, Bontang',
            'foto'      => 'default.pdf',
        ]);

        User::create([
            'nip'           => '2411102441189',                
            'name'          => 'Raditya Andromeda Barito',
            'email'         => 'raditya@example.com',
            'password'      => 'password123',
            'role'          => 'guru',              
            'jenis_kelamin' => 'L',
            'alamat'        => 'Jl. Ahmad Yani No. 12, Bontang',
            'foto'      => 'default.pdf',
        ]);

        User::create([
            'nis'           => '2411102441299',
            'name'          => 'Putri Auliya Lestari',
            'email'         => 'putri@example.com',
            'password'      => 'password123',
            'role'          => 'siswa',
            'jenis_kelamin' => 'P',
            'alamat'        => 'Jl. Merdeka No. 9, Sangatta',
            'foto'      => 'default.pdf',
        ]);
    }
}