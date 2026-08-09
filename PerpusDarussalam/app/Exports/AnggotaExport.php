<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AnggotaExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data_Siswa'   => new AnggotaSiswaSheet(),
            'Data_Guru'    => new AnggotaGuruSheet(),
            'Data_Umum'    => new AnggotaUmumSheet(),
        ];
    }
}

/**
 * Sheet 1: Data Siswa (Berdasarkan NISN)
 */
class AnggotaSiswaSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return User::where('status', 'siswa')->get(); 
    }

    public function headings(): array
    {
        return ['ID Anggota', 'NISN', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Alamat', 'Tanggal Terdaftar'];
    }

    public function map($user): array
    {
        $jk = match($user->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-'
        };

        return [
            $user->id,
            $user->nisn ?? '-',  
            $user->name,
            $user->email,
            $jk,
            $user->alamat ?? '-',
            $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B050']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 1) {
            $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }

        return [];
    }

    public function title(): string
    {
        return 'Data_Siswa';
    }
}

/**
 * Sheet 2: Data Guru (Berdasarkan NIK, filter dari kolom status)
 */
class AnggotaGuruSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return User::where('status', 'guru')->get(); 
    }

    public function headings(): array
    {
        return ['ID Anggota', 'NIK', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Alamat', 'Tanggal Terdaftar'];
    }

    public function map($user): array
    {
        $jk = match($user->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-'
        };

        return [
            $user->id,
            $user->nik ?? '-',
            $user->name,
            $user->email,
            $jk,
            $user->alamat ?? '-',
            $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B050']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 1) {
            $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }

        return [];
    }

    public function title(): string
    {
        return 'Data_Guru';
    }
}

/**
 * Sheet 3: Data Umum (Berdasarkan NIK, filter dari kolom status)
 */
class AnggotaUmumSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return User::where('status', 'umum')->get(); 
    }

    public function headings(): array
    {
        return ['ID Anggota', 'NIK', 'Nama Lengkap', 'Email', 'Jenis Kelamin', 'Alamat', 'Tanggal Terdaftar'];
    }

    public function map($user): array
    {
        $jk = match($user->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-'
        };

        return [
            $user->id,
            $user->nik ?? '-',
            $user->name,
            $user->email,
            $jk,
            $user->alamat ?? '-',
            $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B050']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 1) {
            $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            ]);
        }

        return [];
    }

    public function title(): string
    {
        return 'Data_Umum';
    }
}