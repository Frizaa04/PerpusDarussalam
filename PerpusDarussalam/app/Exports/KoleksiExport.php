<?php

namespace App\Exports;

use App\Models\Book;
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

    public function __construct($date)
    {
        $this->selectedDate = $date ? Carbon::parse($date) : today();
    }

    public function array(): array
    {
        $data = [];

        // 1. Header Tabel Koleksi Buku yang diperluas
        $data[] = [
            'ID Buku', 
            'Kode Buku', 
            'Judul Buku', 
            'Penulis', 
            'Penerbit', 
            'Tahun Terbit', 
            'ISBN', 
            'Tanggal Pembelian', 
            'Stok', 
            'Kategori', 
            'Rak', 
            'Deskripsi', 
            'Cover', 
            'Tanggal Dibuat'
        ];

        // 2. Data Buku (Mengambil relasi kategori)
        $books = Book::with('categories')->get();
        $this->jumlahBuku = $books->count();

        foreach ($books as $book) {
            $categories = $book->categories->pluck('nama')->implode(', ');
            
            $data[] = [
                $book->id,
                $book->kode_buku,
                $book->judul ?? $book->title,
                $book->penulis,
                $book->penerbit,
                $book->tahun_terbit,
                $book->isbn,
                $book->tanggal_pembelian,
                $book->stok,
                $categories ?: '-',
                $book->rak,
                $book->deskripsi,
                $book->cover ?? '-', // Menampilkan nama file cover atau '-' jika kosong
                $book->created_at->format('Y-m-d H:i:s'),
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

        // Karena kolom bertambah sampai huruf N (Total 14 kolom: A sampai N)
        // Terapkan warna hijau ke Header Buku (Baris 1 dari kolom A sampai N)
        $sheet->getStyle('A1:N1')->applyFromArray($styleHeaderHijau);

        // Tambahkan border hitam tipis untuk seluruh isi data buku (Kolom A sampai N)
        if ($this->jumlahBuku > 0) {
            $sheet->getStyle('A2:N' . (1 + $this->jumlahBuku))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }
}