<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Data dummy transaksi peminjaman & pengembalian buku
        $allCirculations = collect([
            (object)[
                'nis' => '2411102441261', 
                'name' => 'Muhammad Al Baihaqi', 
                'book_title' => 'Novel', 
                'borrow_date' => '24/07/2026', 
                'return_date' => '24/07/2026'
            ],
            (object)[
                'nis' => '2411102441211', 
                'name' => 'Febri Hamzah Jemikan Nata', 
                'book_title' => 'Algoritma dan Struktur Data', 
                'borrow_date' => '15/07/2026', 
                'return_date' => '22/07/2026'
            ],
            (object)[
                'nis' => '2411102441212', 
                'name' => 'Muhamad Aditya Nugroho', 
                'book_title' => 'Basis Data Pemula', 
                'borrow_date' => '16/07/2026', 
                'return_date' => '23/07/2026'
            ],
            (object)[
                'nis' => '2411102441214', 
                'name' => 'Rofi Raissa Adiyatma', 
                'book_title' => 'Sistem Jaringan Komputer', 
                'borrow_date' => '17/07/2026', 
                'return_date' => '24/07/2026'
            ]
        ]);

        // Filter pencarian berdasarkan NIS, Nama Siswa, atau Judul Buku
        if ($search) {
            $circulations = $allCirculations->filter(function ($item) use ($search) {
                return false !== stripos($item->name, $search) || 
                       false !== stripos($item->nis, $search) || 
                       false !== stripos($item->book_title, $search);
            });
        } else {
            $circulations = $allCirculations;
        }

        return view('layouts.pages.admin.sirkulasi', compact('circulations', 'search'));
    }
}