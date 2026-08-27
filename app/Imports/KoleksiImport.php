<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Str;

class KoleksiImport implements ToModel, WithHeadingRow
{
    public int $importedCount = 0;
    public array $duplicates = [];

    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function model(array $row)
    {
        $judul = isset($row['judul_buku']) ? trim($row['judul_buku']) : (isset($row['judul']) ? trim($row['judul']) : null);
        if (empty($judul)) {
            return null;
        }

        $penulis = !empty($row['penulis']) ? trim($row['penulis']) : '-';

        // 1. Cek Duplikasi di Database (Cek Judul & Penulis)
        $isDuplicate = Book::whereRaw('LOWER(judul) = ?', [mb_strtolower($judul)])
            ->where(function ($query) use ($penulis) {
                if ($penulis !== '-') {
                    $query->whereRaw('LOWER(penulis) = ?', [mb_strtolower($penulis)]);
                }
            })
            ->exists();

        if ($isDuplicate) {
            $this->duplicates[] = $judul;
            return null; // Lewati baris ini (tidak di-insert)
        }

        // 2. Penanganan Kategori (Split koma & Case-Insensitive)
        // Dipindah ke ATAS, sebelum generate kode, karena kode butuh data kategori
        $rawKategori = !empty($row['kategori']) ? trim((string)$row['kategori']) : 'Umum';
        if (str_contains($rawKategori, ',')) {
            $parts = array_map('trim', explode(',', $rawKategori));
            $rawKategori = $parts[0] !== '' ? $parts[0] : 'Umum';
        }

        $category = Category::whereRaw('LOWER(nama) = ?', [mb_strtolower($rawKategori)])->first();
        if (!$category) {
            $category = Category::create([
                'nama'      => $rawKategori,
                'deskripsi' => '-'
            ]);
        }

        // 3. Generate Kode Buku — PAKAI LOGIKA YANG SAMA DENGAN TAMBAH MANUAL
        $tahunTerbit = !empty($row['tahun_terbit']) ? (int)$row['tahun_terbit'] : date('Y');
        $kodeBuku = $this->bookService->generateBookCode($category, $tahunTerbit, $category->id);

        // 4. Konversi Tanggal Pembelian
        $tanggalPembelian = date('Y-m-d');
        if (!empty($row['tanggal_pembelian'])) {
            if (is_numeric($row['tanggal_pembelian'])) {
                $tanggalPembelian = ExcelDate::excelToDateTimeObject($row['tanggal_pembelian'])->format('Y-m-d');
            } else {
                $time = strtotime($row['tanggal_pembelian']);
                if ($time !== false) {
                    $tanggalPembelian = date('Y-m-d', $time);
                }
            }
        }

        // Increment counter berhasil
        $this->importedCount++;

        // 5. Insert Data Buku Baru
        return new Book([
            'categories_id'     => $category->id,
            'kode_buku'         => $kodeBuku,
            'judul'             => $judul,
            'penulis'           => $penulis,
            'penerbit'          => !empty($row['penerbit']) ? trim($row['penerbit']) : '-',
            'tahun_terbit'      => $tahunTerbit,
            'isbn'              => !empty($row['isbn']) ? trim($row['isbn']) : '-',
            'tanggal_pembelian' => $tanggalPembelian,
            'stok'              => 0,
            'deskripsi'         => $row['deskripsi'] ?? null,
            'rak'               => !empty($row['rak']) ? (string)$row['rak'] : '-',
            'cover'             => null,
        ]);
    }
}