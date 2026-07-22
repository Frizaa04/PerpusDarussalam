<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Opsional jika password tidak otomatis di-hash oleh model

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Data Siswa 1
        User::create([
            'nis'           => '2411102441211',
            'name'          => 'Febri Hamzah Jemikan Nata',
            'email'         => 'febri@example.com', // Kolom baru yang wajib diisi/unik
            'password'      => 'password123',       // Akan otomatis di-hash jika di Model sudah di-set 'hashed'
            'role'          => 'siswa',             // Menggunakan format enum baru
            'jenis_kelamin' => 'L',                 // Tambahan jenis kelamin (L/P)
            'alamat'        => 'Jl. Merdeka No. 10, Bontang',
            'foto'      => 'default.pdf',
        ]);

        // Data Siswa 2
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

        // Data Guru / Admin (Contoh Role Guru atau Umum)
        User::create([
            'nip'           => '69696969696969',                // Guru/Umum bisa dikosongkan (nullable)
            'name'          => 'Raditya Andromeda Barito',
            'email'         => 'raditya@example.com',
            'password'      => 'password123',
            'role'          => 'guru',              // Diubah ke pilihan role sekolah: siswa, guru, atau umum
            'jenis_kelamin' => 'L',
            'alamat'        => 'Jl. Ahmad Yani No. 12, Bontang',
            'foto'      => 'default.pdf',
        ]);
    }
}