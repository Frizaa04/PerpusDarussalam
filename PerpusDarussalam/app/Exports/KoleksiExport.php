<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Ebook; 
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KoleksiExport implements WithMultipleSheets
{
    protected $selectedDate;

    public function __construct($date)
    {
        $this->selectedDate = $date ? Carbon::parse($date) : today();
    }

    // Membagi data menjadi 3 sheet 
    public function sheets(): array
    {
        return [
            'Daftar_Buku' => new DaftarBukuSheet(),
            'Buku_Item'   => new BukuItemSheet(),
            'Data_Ebook'  => new EbookSheet(), 
        ];
    }
}

/**
 * Sheet 1: Daftar Buku
 */
class DaftarBukuSheet implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected $jumlahBuku = 0;

    public function array(): array
    {
        $data = [];

        $data[] = [
            'ID Buku', 'Kode Buku', 'Judul Buku', 'Penulis', 'Penerbit', 
            'Tahun Terbit', 'ISBN', 'Tanggal Pembelian', 'Stok', 'Kategori', 
            'Rak', 'Deskripsi', 'Cover', 'Tanggal Dibuat'
        ];

        $books = Book::with('categories')->get();
        $this->jumlahBuku = $books->count();

        foreach ($books as $book) {
            $categoryName = $book->categories->nama ?? '-';
            
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
                $categoryName,
                $book->rak,
                $book->deskripsi,
                $book->cover ?? '-',
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

        $sheet->getStyle('A1:N1')->applyFromArray($styleHeaderHijau);

        if ($this->jumlahBuku > 0) {
            $sheet->getStyle('A2:N' . (1 + $this->jumlahBuku))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }

    public function title(): string
    {
        return 'Daftar_Buku';
    }
}

/**
 * Sheet 2: Buku Item (Rincian per unit buku)
 */
class BukuItemSheet implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected $jumlahItem = 0;

    public function array(): array
    {
        $data = [];

        $data[] = [
            'Judul Buku', 
            'Nomor Inventaris', 
            'Kondisi'
        ];

        $items = BookItem::with('book')->get();
        $this->jumlahItem = $items->count();

        foreach ($items as $item) {
            $data[] = [
                $item->book->judul ?? 'Buku Tidak Ditemukan',
                $item->nomor_inventaris,
                ucfirst(str_replace('_', ' ', $item->kondisi)),
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

        $sheet->getStyle('A1:C1')->applyFromArray($styleHeaderHijau);

        if ($this->jumlahItem > 0) {
            $sheet->getStyle('A2:C' . (1 + $this->jumlahItem))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }

    public function title(): string
    {
        return 'Buku_Item';
    }
}

/**
 * Sheet 3: Data E-Book (Tempat khusus E-Book)
 */
class EbookSheet implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected $jumlahEbook = 0;

    public function array(): array
    {
        $data = [];

        $data[] = [
            'ID Ebook', 'Kode Ebook', 'Judul Ebook', 'Penulis', 'Penerbit', 
            'Tahun Terbit', 'ISBN', 'Kategori', 'File PDF', 'Tanggal Dibuat'
        ];

        // Load data ebook beserta relasi kategorinya
        $ebooks = Ebook::with('category')->get();
        $this->jumlahEbook = $ebooks->count();

        foreach ($ebooks as $ebook) {
            $data[] = [
                $ebook->id,
                $ebook->kode_ebook,
                $ebook->judul,
                $ebook->penulis,
                $ebook->penerbit,
                $ebook->tahun_terbit,
                $ebook->isbn ?? '-',
                $ebook->category->nama ?? '-', 
                $ebook->file_pdf,              
                $ebook->created_at->format('Y-m-d H:i:s'),
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

        $sheet->getStyle('A1:J1')->applyFromArray($styleHeaderHijau);

        if ($this->jumlahEbook > 0) {
            $sheet->getStyle('A2:J' . (1 + $this->jumlahEbook))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }

    public function title(): string
    {
        return 'Ebook';
    }
}
