<?php

namespace App\Exports;

use App\Models\Borrowing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BorrowingExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $jumlahPeminjaman = 0;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate   = Carbon::parse($endDate)->endOfDay();
    }

    public function array(): array
    {
        $data = [];

        $data[] = [
            'ID Peminjaman',
            'Nama Peminjam',
            'Judul Buku',
            'Status',
            'Tanggal Pinjam'
        ];

        $borrowings = Borrowing::with([
                'user',
                'bookItem.book'
            ])
            ->whereBetween('tanggal_pinjam', [
                $this->startDate,
                $this->endDate
            ])
            ->orderBy('tanggal_pinjam', 'asc')
            ->get();

        $this->jumlahPeminjaman = $borrowings->count();

        foreach ($borrowings as $borrowing) {
            $data[] = [
                $borrowing->id,

                $borrowing->user?->name ?? '-',

                $borrowing->bookItem?->book?->judul
                    ?? $borrowing->bookItem?->book?->title
                    ?? '-',

                $borrowing->status ?? '-',

                $borrowing->tanggal_pinjam
                    ? Carbon::parse($borrowing->tanggal_pinjam)
                        ->format('Y-m-d H:i:s')
                    : '-',
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
                    'argb' => '00B050'
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

        $sheet->getStyle('A1:E1')
            ->applyFromArray($styleHeaderHijau);

        if ($this->jumlahPeminjaman > 0) {
            $sheet->getStyle(
                'A2:E' . ($this->jumlahPeminjaman + 1)
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