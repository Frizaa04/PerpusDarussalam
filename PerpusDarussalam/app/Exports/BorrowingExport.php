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
    protected $selectedDate;
    protected $jumlahPeminjaman = 0;

    public function __construct($date)
    {
        $this->selectedDate = $date ? Carbon::parse($date) : today();
    }
    
    public function array(): array
    {
        $data = [];

        // Header Tabel Peminjaman
        $data[] = ['ID Peminjaman', 'Nama Peminjam', 'Judul Buku', 'Status', 'Tanggal Pinjam'];

        // Data Peminjaman
        $borrowings = Borrowing::with(['user', 'bookItem.book'])->get();
        $this->jumlahPeminjaman = $borrowings->count();

        foreach ($borrowings as $borrowing) {
            $data[] = [
                $borrowing->id,
                $borrowing->user->name ?? '-',
                $borrowing->bookItem->book->judul ?? $borrowing->bookItem->book->title ?? '-',
                $borrowing->status,
                $borrowing->created_at->format('Y-m-d H:i:s'),
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
                'startColor' => ['argb' => '00B050'], 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Terapkan warna hijau ke Header Peminjaman (Baris 1)
        $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderHijau);

        // Tambahkan border hitam tipis untuk seluruh isi data
        if ($this->jumlahPeminjaman > 0) {
            $sheet->getStyle('A2:E' . (1 + $this->jumlahPeminjaman))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }
}