<?php

namespace App\Exports;

use App\Models\Visits;
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

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate   = Carbon::parse($endDate)->endOfDay();
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = [
            'No',
            'Waktu Kunjungan',
            'No Identitas / ID',
            'Nama Pengunjung',
            'Kategori / Role'
        ];

        // Ambil data berdasarkan rentang tanggal
        $visits = Visits::with('user')
            ->whereBetween('visited_at', [
                $this->startDate,
                $this->endDate
            ])
            ->orderBy('visited_at', 'asc')
            ->get();

        $this->jumlahData = $visits->count();

        $no = 1;

        foreach ($visits as $visit) {
            $user = $visit->user;

            $data[] = [
                $no++,

                $visit->visited_at
                    ? Carbon::parse($visit->visited_at)->format('Y-m-d H:i:s')
                    : '-',

                $user?->nis
                    ?? $user?->nik
                    ?? $user?->id
                    ?? '-',

                $user?->name ?? '-',

                $user?->role
                    ?? $user?->kategori
                    ?? '-',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleHeaderHijau = [
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => Color::COLOR_WHITE
                ],
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '004D40'
                ],
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'argb' => '000000'
                    ],
                ],
            ],
        ];

        // Style header
        $sheet->getStyle('A1:E1')
            ->applyFromArray($styleHeaderHijau);

        // Border data
        if ($this->jumlahData > 0) {
            $sheet->getStyle(
                'A2:E' . ($this->jumlahData + 1)
            )
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(
                    Border::BORDER_THIN
                );
        }

        return [];
    }
}