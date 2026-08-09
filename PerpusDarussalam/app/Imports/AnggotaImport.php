<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AnggotaImport implements ToModel, WithHeadingRow
{
    public int $importedCount = 0;
    public array $duplicates = [];

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

        // --- PENGECEKAN DUPLIKAT ---
        // Pengecekan awal berdasarkan NIS / NIK jika diset di Excel
        $queryDuplicate = User::query();

        if ($nis || $nik || $email) {
            $queryDuplicate->where(function ($q) use ($nis, $nik, $email) {
                if ($nis) $q->orWhere('nis', $nis);
                if ($nik) $q->orWhere('nik', $nik);
                if ($email) $q->orWhere('email', $email);
            });
        } else {
            // Jika email, nis, nik kosong, cek berdasarkan Nama
            $queryDuplicate->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($nama))]);
        }

        // Jika data sudah ada di database, catat sebagai duplikat & skip
        if ($queryDuplicate->exists()) {
            $this->duplicates[] = $nama;
            return null;
        }

        // Generate email acak jika tidak diisi di Excel
        if (!$email) {
            $email = strtolower(str_replace(' ', '', $nama)) . rand(100, 999) . '@example.com';
        }

        // Penentuan role
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