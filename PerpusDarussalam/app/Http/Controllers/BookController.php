<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Data dummy daftar buku
        $allBooks = collect([
            (object)['cover' => null, 'judul' => 'Ivanna', 'kategori' => 'Novel', 'stok' => 100],
            (object)['cover' => null, 'judul' => 'Matematika Dasar', 'kategori' => 'Pelajaran', 'stok' => 90],
            (object)['cover' => null, 'judul' => 'Laskar Pelangi', 'kategori' => 'Novel', 'stok' => 80],
            (object)['cover' => null, 'judul' => 'Laut Bercerita', 'kategori' => 'Novel', 'stok' => 70],
            (object)['cover' => null, 'judul' => 'Negeri 5 Menara', 'kategori' => 'Novel', 'stok' => 60],
            (object)['cover' => null, 'judul' => 'The Little Prince', 'kategori' => 'Novel', 'stok' => 60],
            (object)['cover' => null, 'judul' => 'The Alchemist', 'kategori' => 'Novel', 'stok' => 50],
            (object)['cover' => null, 'judul' => 'Cantik Itu Luka', 'kategori' => 'Novel', 'stok' => 40],
        ]);

        // Logika filter pencarian
        if ($search) {
            $books = $allBooks->filter(function ($book) use ($search) {
                return false !== stripos($book->judul, $search) || false !== stripos($book->kategori, $search);
            });
        } else {
            $books = $allBooks;
        }

        return view('layouts.pages.admin.katalog_buku', compact('books', 'search'));
    }
}