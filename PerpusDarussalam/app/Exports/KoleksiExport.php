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
        // 1. Header Buku
        $data[] = ['ID Buku', 'Judul Buku', 'Stok', 'Kategori', 'Tanggal Dibuat'];

        // 2. Data Buku
        $books = Book::with('categories')->get();
        $this->jumlahBuku = $books->count(); // Simpan jumlah buku untuk menghitung posisi baris Excel nanti

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
        // 3. Tambahkan 2 baris kosong agar tabel tidak menempel
        $data[] = ['', '', '', '', ''];
        $data[] = ['', '', '', '', ''];


        // ================= TABEL PEMINJAMAN =================
        // 4. Header Peminjaman
        $data[] = ['ID Peminjaman', 'Nama Peminjam', 'Judul Buku', 'Status', 'Tanggal Pinjam'];

        // 5. Data Peminjaman
        $borrowings = Borrowing::with(['user', 'book'])->get();
        $this->jumlahPeminjaman = $borrowings->count();

        foreach ($borrowings as $borrowing) {
            $data[] = [
                $borrowing->id,
                $borrowing->user->name ?? '-',
                $borrowing->book->title ?? $borrowing->book->judul ?? '-',
                $borrowing->status,
                $borrowing->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Menentukan di baris ke berapa header peminjaman berada
        // Rumus: 1 (Header Buku) + Jumlah Buku + 2 (Baris Kosong) + 1 (Header Peminjaman)
        $barisHeaderPeminjaman = 1 + $this->jumlahBuku + 2 + 1;

        // Pengaturan Gaya Tabel (Background Hijau & Teks Putih Bold)
        $styleHeaderHijau = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00B050'], // Warna Hijau seperti di gambar
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Garis hitam pinggiran tabel
                ],
            ],
        ];

        // 1. Terapkan warna hijau ke Header Buku (Baris 1 dari kolom A sampai E)
        $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderHijau);

        // 2. Terapkan warna hijau ke Header Peminjaman
        $sheet->getStyle('A' . $barisHeaderPeminjaman . ':E' . $barisHeaderPeminjaman)->applyFromArray($styleHeaderHijau);

        // (Opsional) Tambahkan border hitam tipis untuk semua baris isi data agar lebih rapi
        $sheet->getStyle('A2:E' . (1 + $this->jumlahBuku))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $barisAkhirPeminjaman = $barisHeaderPeminjaman + $this->jumlahPeminjaman;
        $sheet->getStyle('A' . ($barisHeaderPeminjaman + 1) . ':E' . $barisAkhirPeminjaman)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}