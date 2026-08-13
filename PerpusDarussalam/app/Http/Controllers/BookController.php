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
        // Jalankan validasi buku
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

        // Simpan data buku lewat Service
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

    public function destroyMultiple(Request $request)
    {
        // 1. Ambil array ID yang dikirim dari AJAX
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            // 2. Proses hapus data buku berdasarkan array ID
            \App\Models\Book::whereIn('id', $ids)->delete();
            
            // 3. WAJIB RETURN JSON (Ini yang bikin AJAX sukses dan gak masuk blok error lagi)
            return response()->json([
                'success' => true,
                'message' => 'Data buku berhasil dihapus lintas halaman!'
            ], 200);
        }

        // Jika array ID kosong
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data buku yang dipilih.'
        ], 400);
    }
    // Tambah Kategori langsung dari tombol di halaman utama
    public function storeCategory(Request $request)
    {
        $request->validateWithBag('categoryStoreForm', [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama',
        ], [
            'nama_kategori.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        Category::create([
            'nama'      => $request->nama_kategori,
            'deskripsi' => '-',
        ]);

        return redirect()
            ->route('book.index')
            ->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function destroyCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('book.index')->with('error', 'Kategori tidak ditemukan.');
        }

        $jumlahBuku = Book::where('categories_id', $id)->count();

        if ($jumlahBuku > 0) {
            return redirect()->route('book.index')->with('error', "Kategori '{$category->nama}' masih dipakai oleh {$jumlahBuku} buku. Pindahkan atau hapus buku tersebut terlebih dahulu sebelum menghapus kategori ini.");
        }

        $category->delete();

        return redirect()->route('book.index')->with('success', "Kategori '{$category->nama}' berhasil dihapus!");
    }
}