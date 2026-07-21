<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');

        $queryBuilder = Book::with('categories');

        if ($search) {
            $queryBuilder->where(function ($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('kode_buku', 'LIKE', "%{$search}%")
                  ->orWhere('penulis', 'LIKE', "%{$search}%")
                  ->orWhere('penerbit', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%")
                  ->orWhere('rak', 'LIKE', "%{$search}%")
                  ->orWhere('tanggal_pembelian', 'LIKE', "%{$search}%")
                  ->orWhereHas('categories', function ($catQuery) use ($search) {
                      $catQuery->where('nama', 'LIKE', "%{$search}%");
                  });
            });
        }

        $books = $queryBuilder->get();

        $allCategories = Category::all();

        return view('layouts.pages.admin.katalog_buku', compact('books', 'search', 'allCategories'));
    }


    public function store(Request $request)
{
    // 1. Validasi sesuai field yang dikirim dari HTML FE lu saat ini
    $request->validate([
        'judul'     => 'required|string|max:255',
        'kategori'  => 'nullable|string',
        'stok'      => 'required|numeric',
        'deskripsi' => 'nullable|string',
        'rak'       => 'nullable|string',
        'cover'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Cari ID kategori berdasarkan nama kategori yang dikirim dari form (misal: "Novel")
    // Jika tidak ketemu, pakai ID kategori pertama sebagai fallback
    $category = Category::where('nama', $request->kategori)->first();
    $categoryId = $category ? $category->id : (Category::first()->id ?? 1);

    $coverPath = null;
    if ($request->hasFile('cover')) {
        $coverPath = $request->file('cover')->store('covers', 'public');
    }

    // 2. Simpan ke database dengan nilai default untuk field yang tidak ada di Form FE
    Book::create([
        'categories_id' => $categoryId,
        'kode_buku'     => 'BK-' . time(), // Dibuat otomatis unik jika FE belum ada input kode_buku
        'judul'         => $request->judul,
        'penulis'       => $request->penulis ?? 'Anonim',
        'penerbit'      => $request->penerbit ?? 'Umum',
        'isbn'          => $request->isbn ?? '000-0-00-000000-0',
        'tanggal_pembelian' => $request->tanggal_pembelian ?? now()->toDateString(),
        'deskripsi'     => $request->deskripsi ?? null,
        'rak'           => $request->rak ?? null,
        'tahun_terbit'  => $request->tahun_terbit ?? date('Y'),
        'stok'          => $request->stok,
        'cover'         => $coverPath,
    ]);

    return redirect()->route('book.index')->with('success', 'Buku baru berhasil ditambahkan ke database!');
}

public function update(Request $request)
{
    // 1. Cari buku
    $book = Book::findOrFail($request->id);

    // 2. Jika categories_id yang dikirim dari form berupa NAMA KATEGORI (bukan ID angka),
    // kita cari/buatkan ID-nya dulu secara otomatis!
    $categoryId = $request->categories_id;
    if (!is_numeric($categoryId)) {
        $category = \App\Models\Category::firstOrCreate(['nama' => $categoryId ?? 'Umum']);
        $categoryId = $category->id;
    }

    // 3. Validasi
    $request->validate([
        'kode_buku'         => 'required|string|max:255|unique:books,kode_buku,' . $book->id,
        'judul'             => 'required|string|max:255',
        'penulis'           => 'required|string|max:255',
        'penerbit'          => 'required|string|max:255',
        'isbn'              => 'required|string|max:255',
        'tahun_terbit'      => 'required|digits:4',
        'tanggal_pembelian' => 'required',
        'deskripsi'         => 'nullable|string',
        'rak'               => 'nullable|string',
        'stok'              => 'required|integer|min:0',
        'cover'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // 4. Siapkan Data Update
    $dataToUpdate = [
        'kode_buku'         => $request->kode_buku,
        'categories_id'     => $categoryId, // Pake ID yang terjamin ada
        'judul'             => $request->judul,
        'penulis'           => $request->penulis,
        'penerbit'          => $request->penerbit,
        'isbn'              => $request->isbn,
        'tanggal_pembelian' => date('Y-m-d', strtotime($request->tanggal_pembelian)),
        'tahun_terbit'      => $request->tahun_terbit,
        'stok'              => $request->stok,
        'deskripsi'         => $request->deskripsi ?? null,
        'rak'               => $request->rak ?? null,
    ];

    // 5. Handle Cover
    if ($request->hasFile('cover')) {
        if ($book->cover && \Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover);
        }
        $dataToUpdate['cover'] = $request->file('cover')->store('covers', 'public');
    }

    // 6. Eksekusi Update
    $book->update($dataToUpdate);

    return redirect()->route('book.index')->with('success', 'Data buku berhasil diperbarui!');
}


    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('book.index')->with('success', 'Buku berhasil dihapus dari database!');
    }
}