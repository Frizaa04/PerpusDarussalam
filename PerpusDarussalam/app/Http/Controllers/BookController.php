<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookItem;
use App\Services\BookService; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

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

    public function store(Request $request, BookService $bookService)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'nullable|string',
            'stok'      => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string',
            'rak'       => 'nullable|string',
            'cover'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bookService->createBook($request->all());

        return redirect()
            ->route('book.index')
            ->with('success', 'Buku baru dan nomor inventaris berhasil ditambahkan!');
    }

    public function update(Request $request, BookService $bookService)
    {
        $book = Book::findOrFail($request->id);
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
        $bookService->updateBook($book, $request);
        return redirect()
            ->route('book.index')
            ->with('success', 'Data buku dan inventaris berhasil diperbarui!');
    }

    public function destroy($id, BookService $bookService)
    {
        $book = Book::findOrFail($id);

        $bookService->deleteBook($book);

        return redirect()
            ->route('book.index')
            ->with('success','Buku berhasil dihapus.');
    }
}