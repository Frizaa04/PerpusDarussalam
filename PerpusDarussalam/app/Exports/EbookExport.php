<?php

namespace App\Exports;

use App\Models\Ebook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EbookExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Ebook::select('id', 'kode_ebook', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'isbn', 'kategori')->get();
    }

    public function headings(): array
    {
        return [
            'ID Ebook',
            'Kode Ebook',
            'Judul Ebook',
            'Penulis',
            'Penerbit',
            'Tahun Terbit',
            'ISBN',
            'Kategori'
        ];
    }

    public function title(): string
    {
        return 'Ebook'; 
    }
}