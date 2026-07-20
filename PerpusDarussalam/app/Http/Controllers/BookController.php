<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Data dummy daftar buku (ditambahkan atribut deskripsi dan rak)
        $allBooks = collect([
            (object)['cover' => null, 'judul' => 'Ivanna', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 100, 'rak' => '01'],
            (object)['cover' => null, 'judul' => 'Matematika Dasar', 'deskripsi' => '...', 'kategori' => 'Pelajaran', 'stok' => 90, 'rak' => '02'],
            (object)['cover' => null, 'judul' => 'Laskar Pelangi', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 80, 'rak' => '01'],
            (object)['cover' => null, 'judul' => 'Laut Bercerita', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 70, 'rak' => '03'],
            (object)['cover' => null, 'judul' => 'Negeri 5 Menara', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 60, 'rak' => '01'],
            (object)['cover' => null, 'judul' => 'The Little Prince', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 60, 'rak' => '04'],
            (object)['cover' => null, 'judul' => 'The Alchemist', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 50, 'rak' => '02'],
            (object)['cover' => null, 'judul' => 'Cantik Itu Luka', 'deskripsi' => '...', 'kategori' => 'Novel', 'stok' => 40, 'rak' => '03'],
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

    /**
     * Menyimpan data buku baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'stok' => 'required|numeric',
            'rak' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Logika upload foto cover jika ada
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
        }

        // Catatan: Jika sudah terhubung ke database, gunakan: Book::create($request->all());

        return redirect()->route('book.index')->with('success', 'Buku baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui data buku
     */
    public function update(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'stok' => 'required|numeric',
            'rak' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Catatan: Jika sudah terhubung ke database, cari dan update data berdasarkan ID/Judul:
        // $book = Book::where('judul', $request->judul)->first();
        // $book->update($request->all());

        return redirect()->route('book.index')->with('success', 'Data buku berhasil diperbarui!');
    }
}