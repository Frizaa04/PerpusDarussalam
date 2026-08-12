<?php

namespace App\Exports;

use App\Models\visits;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $jumlahData = 0;

    // Terima rentang tanggal dari Controller
    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }
    
    public function array(): array
    {
        $data = [];

        $data[] = ['No', 'Waktu Kunjungan', 'No Identitas / ID', 'Nama Pengunjung', 'Kategori / Role'];

        // whereBetween untuk mengambil data sesuai rentang (Harian/Mingguan/Bulanan)
        $visits = visits::with('user')
            ->whereBetween('visited_at', [
                $this->startDate->format('Y-m-d 00:00:00'), 
                $this->endDate->format('Y-m-d 23:59:59')
            ])
            ->get();
            
        $this->jumlahData = $visits->count();
        $no = 1;

        foreach ($visits as $visit) {
            $data[] = [
                $no++,
                $visit->visited_at ? Carbon::parse($visit->visited_at)->format('Y-m-d H:i:s') : '-',
                $visit->user->nis ?? $visit->user->nik ?? $visit->user->id ?? '-', 
                $visit->user->name ?? '-',
                $visit->user->role ?? $visit->user->kategori ?? '-', 
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleHeaderHijau = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '004D40'], 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderHijau);

        if ($this->jumlahData > 0) {
            $sheet->getStyle('A2:E' . (1 + $this->jumlahData))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }
}