<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AnggotaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return User::select([
            'id',
            'nis',
            'nip',
            'nik',
            'name',
            'email',
            'jenis_kelamin',
            'alamat',
            'created_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID Anggota',
            'NIS',
            'NIP',
            'NIK',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'Alamat',
            'Tanggal Terdaftar',
        ];
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
            $user->nis ?? '-',
            $user->nip ?? '-',
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
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00B050'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:I{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'], 
                ],
            ],
        ]);

        return [];
    }
}