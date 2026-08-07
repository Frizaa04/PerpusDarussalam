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

        $books = $queryBuilder->paginate(10)->withQueryString();
        $allCategories = Category::all();

        return view('layouts.pages.admin.katalog_buku', compact('books', 'search', 'allCategories'));
    }

    public function store(Request $request, BookService $bookService)
    {
        // 1. Cek jika admin ingin MENGHAPUS kategori
        if ($request->filled('delete_category_id')) {
            $category = Category::find($request->delete_category_id);
            if ($category) {
                // Opsional: Cek apakah kategori masih dipakai buku
                // if ($category->books()->count() > 0) { ... }
                $category->delete();
            }
            return redirect()
                ->route('book.index')
                ->with('success', 'Kategori berhasil dihapus!');
        }

        // 2. Cek jika admin melakukan Edit Nama Kategori yang ada
        if ($request->filled('edit_category_id') && $request->filled('kategori_baru')) {
            $category = Category::find($request->edit_category_id);
            if ($category) {
                $category->update(['nama' => $request->kategori_baru]);
            }
            $request->merge(['categories_id' => $request->edit_category_id]);
        }
        // 3. Cek jika admin mengetik Kategori Baru
        elseif ($request->filled('kategori_baru')) {
            $newCategory = Category::create([
                'nama' => $request->kategori_baru
            ]);
            $request->merge(['categories_id' => $newCategory->id]);
        }

        // 4. Jalankan validasi buku
        $request->validateWithBag('bookStoreForm', [
            'categories_id'     => 'required|exists:categories,id',
            'judul'             => 'required|string|max:255',
            'penulis'           => 'required|string|max:255',
            'penerbit'          => 'required|string|max:255',
            'tahun_terbit'      => 'required|digits:4',
            'isbn'              => 'required|string|max:255',
            'tanggal_pembelian' => 'required|date',
            'stok'              => 'required|integer|min:0',
            'cover'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'deskripsi'         => 'required|string',
            'rak'               => 'required|string|max:255',
        ]);

        // 5. Simpan data buku lewat Service
        $bookService->createBook($request->all());

        return redirect()
            ->route('book.index')
            ->with('success', 'Data buku berhasil ditambahkan!');
    }

    public function update(Request $request, BookService $bookService)
    {
        $book = Book::findOrFail($request->id);
        
        $request->validateWithBag('bookUpdateForm', [
            'categories_id'     => 'required|exists:categories,id', 
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

    public function destroyMultiple(Request $request, BookService $bookService)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada buku yang dipilih untuk dihapus.');
        }

        // Ambil semua buku berdasarkan ID yang dicentang
        $books = Book::whereIn('id', $ids)->get();

        foreach ($books as $book) {
            
            $bookService->deleteBook($book);
        }

        return redirect()
            ->route('book.index')
            ->with('success', 'Buku yang dipilih berhasil dihapus!');
    }
}