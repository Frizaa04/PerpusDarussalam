<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
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
    protected $startDate;
    protected $endDate;

    // Terima parameter tanggal dari Controller
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            'Data_Siswa'   => new AnggotaSiswaSheet($this->startDate, $this->endDate),
            'Data_Guru'    => new AnggotaGuruSheet($this->startDate, $this->endDate),
            'Data_Umum'    => new AnggotaUmumSheet($this->startDate, $this->endDate),
        ];
    }
}

/**
 * Sheet 1: Data Siswa
 */
class AnggotaSiswaSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return User::where('status', 'siswa')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->format('Y-m-d 00:00:00'), 
                Carbon::parse($this->endDate)->format('Y-m-d 23:59:59')
            ])
            ->get(); 
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
 * Sheet 2: Data Guru
 */
class AnggotaGuruSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return User::where('status', 'guru')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->format('Y-m-d 00:00:00'), 
                Carbon::parse($this->endDate)->format('Y-m-d 23:59:59')
            ])
            ->get(); 
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
 * Sheet 3: Data Umum
 */
class AnggotaUmumSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return User::where('status', 'umum')
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->format('Y-m-d 00:00:00'), 
                Carbon::parse($this->endDate)->format('Y-m-d 23:59:59')
            ])
            ->get(); 
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