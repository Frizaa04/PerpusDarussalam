<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EbookController extends Controller
{
    /**
     * Menampilkan Halaman Katalog E-Book (UI Only)
     */
    public function index()
    {
        // Data dummy statis untuk keperluan tampilan UI
        $ebooks = [
            [
                'id' => 1,
                'judul' => 'Pemrograman Web dengan Laravel 11',
                'penulis' => 'Rahmat Hidayat',
                'kategori' => 'Teknologi',
                'tahun' => '2024',
                'file_pdf' => 'sample.pdf',
            ],
            [
                'id' => 2,
                'judul' => 'Sejarah Kebudayaan Islam Klasik',
                'penulis' => 'Ahmad Nur',
                'kategori' => 'Sejarah',
                'tahun' => '2023',
                'file_pdf' => 'sample.pdf',
            ],
            [
                'id' => 3,
                'judul' => 'Dasar-Dasar Keamanan Jaringan',
                'penulis' => 'Budi Santoso',
                'kategori' => 'Teknologi',
                'tahun' => '2022',
                'file_pdf' => 'sample.pdf',
            ]
        ];

        return view('layouts.pages.admin.ebook', compact('ebooks'));
    }
}