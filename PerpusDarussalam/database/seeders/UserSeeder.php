<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan Model User di-import

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Masukkan data contoh ke tabel users
        User::create([
            'nis'      => '2411102441211',
            'name'     => 'Febri Hamzah Jemikan Nata',
            'alamat'   => 'Jl. Merdeka No. 10, Bontang',
            'file_url' => 'default.pdf',
            'role'     => 'Siswa',
        ]);

        User::create([
            'nis'      => '2411102441212',
            'name'     => 'Muhamad Aditya Nugroho',
            'alamat'   => 'Jl. Pahlawan No. 5, Bontang',
            'file_url' => 'default.pdf',
            'role'     => 'Siswa',
        ]);

        User::create([
            'nis'      => '2411102441217',
            'name'     => 'Raditya Andromeda Barito',
            'alamat'   => 'Jl. Ahmad Yani No. 12, Bontang',
            'file_url' => 'default.pdf',
            'role'     => 'Admin',
        ]);
    }
}