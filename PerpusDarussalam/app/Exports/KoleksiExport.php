<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KoleksiExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $selectedDate;
    protected $jumlahBuku = 0;
    protected $jumlahPeminjaman = 0;

    public function __construct($date)
    {
        $this->selectedDate = $date ? Carbon::parse($date) : today();
    }

    public function array(): array
    {
        $data = [];

        // ================= TABEL KOLEKSI =================
        // Header Buku
        $data[] = ['ID Buku', 'Judul Buku', 'Stok', 'Kategori', 'Tanggal Dibuat'];

        // Data Buku
        $books = Book::with('categories')->get();
        $this->jumlahBuku = $books->count();

        foreach ($books as $book) {
            $categories = $book->categories->pluck('nama')->implode(', ');
            $data[] = [
                $book->id,
                $book->title ?? $book->judul,
                $book->stok,
                $categories ?: '-',
                $book->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // ================= PEMISAH =================
        // Menambahkan 2 baris kosong agar tabel tidak menempel
        $data[] = ['', '', '', '', ''];
        $data[] = ['', '', '', '', ''];


        // ================= TABEL PEMINJAMAN =================
        // Header Peminjaman
        $data[] = ['ID Peminjaman', 'Nama Peminjam', 'Judul Buku', 'Status', 'Tanggal Pinjam'];

        // Data Peminjaman
        $borrowings = Borrowing::with(['user', 'bookItem'])->get();
        $this->jumlahPeminjaman = $borrowings->count();

        foreach ($borrowings as $borrowing) {
            $data[] = [
                $borrowing->id,
                $borrowing->user->name ?? '-',
                $borrowing->bookItem->book->title ?? $borrowing->bookItem->book->judul ?? '-',
                $borrowing->status,
                $borrowing->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        /* Menentukan di baris ke berapa header peminjaman berada
         * Rumus: 1 (Header Buku) + Jumlah Buku + 2 (Baris Kosong) + 1 (Header Peminjaman) */
        $barisHeaderPeminjaman = 1 + $this->jumlahBuku + 2 + 1;

        /*
        Pengaturan tabel
        - Background tabel untuk header berwarna hijau
        - Border bagian luar berwarna hitam
        */
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

        // Terapkan warna hijau ke Header Buku (Baris 1 dari kolom A sampai E)
        $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderHijau);

        // Terapkan warna hijau ke Header Peminjaman
        $sheet->getStyle('A' . $barisHeaderPeminjaman . ':E' . $barisHeaderPeminjaman)->applyFromArray($styleHeaderHijau);

        // Tambahkan border hitam tipis untuk semua baris isi data agar lebih rapi
        $sheet->getStyle('A2:E' . (1 + $this->jumlahBuku))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $barisAkhirPeminjaman = $barisHeaderPeminjaman + $this->jumlahPeminjaman;
        $sheet->getStyle('A' . ($barisHeaderPeminjaman + 1) . ':E' . $barisAkhirPeminjaman)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}