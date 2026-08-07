<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AnggotaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $nama = $row['nama_lengkap'] ?? $row['nama'] ?? null;

        if (empty($nama)) {
            return null;
        }

        $cleanNumber = function ($value) {
            if (empty($value) || trim($value) === '-') return null;
    
            $val = trim($value);
            
            if (stripos($val, 'e') !== false) {
                $val = sprintf('%.0f', (float) $val);
            }
            
            return $val;
        };

        $nisn = $cleanNumber($row['nisn'] ?? null);
        $nik = $cleanNumber($row['nik'] ?? null);
        $email = strtolower(trim($row['email'] ?? ''));
        $alamat = trim($row['alamat'] ?? '');

        if (!$email) {
            $email = strtolower(str_replace(' ', '', $nama)) . rand(100, 999) . '@example.com';
        }

        // Penentuan role disesuaikan
        $role = 'siswa';
        $inputRole = strtolower(trim($row['role'] ?? $row['kategori'] ?? ''));

        if ($inputRole) {
            if (in_array($inputRole, ['siswa', 'guru', 'umum'])) {
                $role = $inputRole;
            }
        } else {
            // Otomatisasi jika kolom role di Excel tidak diisi
            if ($nik && !$nisn) {
                $role = 'guru'; 
            }
        }

        $jkRaw = strtolower(trim($row['jenis_kelamin'] ?? ''));
        $jk = match (true) {
            str_contains($jkRaw, 'laki') || $jkRaw === 'l' => 'L',
            str_contains($jkRaw, 'perem') || $jkRaw === 'p' => 'P',
            default => null,
        };

        return User::updateOrCreate(
            ['email' => $email],
            [
                'nisn'          => $nisn,
                'nik'           => $nik,
                'name'          => $nama,
                'password'      => Hash::make('12345678'), 
                'role'          => $role,
                'jenis_kelamin' => $jk,
                'alamat'        => $alamat,
            ]
        );
    }
}