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

        $nis = $cleanNumber($row['nis'] ?? null);
        $nip = $cleanNumber($row['nip'] ?? null);
        $nik = $cleanNumber($row['nik'] ?? null);
        $email = strtolower(trim($row['email'] ?? ''));
        $alamat = trim($row['alamat'] ?? '');

        if (!$email) {
            $email = strtolower(str_replace(' ', '', $nama)) . rand(100, 999) . '@example.com';
        }

        $role = 'siswa';
        if ($nip) {
            $role = 'guru';
        } elseif ($nik && !$nis) {
            $role = 'umum';
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
                'nis'           => $nis,
                'nip'           => $nip,
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