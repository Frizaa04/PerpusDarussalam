<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookItem; 
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

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'kategori'  => 'nullable|string',
            'stok'      => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string',
            'rak'       => 'nullable|string',
            'cover'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = Category::where('nama', $request->kategori)->first();
        $categoryId = $category ? $category->id : (Category::first()->id ?? 1);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
        }

        return DB::transaction(function () use ($request, $category, $categoryId, $coverPath) {
            
            // Buat format kode buku yang rapi dan terstruktur
            // Contoh hasil: FIK-2026-001 (Kategori - Tahun Terbit - Nomor Urut)
            $kategoriSingkatan = $category ? strtoupper(substr($category->nama, 0, 3)) : 'UMM';
            $tahun = $request->tahun_terbit ?? date('Y');
            
            // Hitung urutan berdasarkan kategori yang sama agar penomorannya kontinu
            $urutan = Book::where('categories_id', $categoryId)->count() + 1;
            $nomorUrut = str_pad($urutan, 3, '0', STR_PAD_LEFT);
            
            $kodeBukuRapi = $kategoriSingkatan . '-' . $tahun . '-' . $nomorUrut;

            // Simpan data buku utama
            $book = Book::create([
                'categories_id'     => $categoryId,
                'kode_buku'         => $kodeBukuRapi, 
                'judul'             => $request->judul,
                'penulis'           => $request->penulis ?? 'Anonim',
                'penerbit'          => $request->penerbit ?? 'Umum',
                'isbn'              => $request->isbn ?? '000-0-00-000000-0',
                'tanggal_pembelian' => $request->tanggal_pembelian ?? now()->toDateString(),
                'deskripsi'         => $request->deskripsi ?? null,
                'rak'               => $request->rak ?? null,
                'tahun_terbit'      => $tahun,
                'stok'              => $request->stok,
                'cover'             => $coverPath,
            ]);

            // Otomatis generate eksemplar fisik ke tabel book_items berdasarkan stok
            for ($i = 1; $i <= $request->stok; $i++) {
                BookItem::create([
                    'book_id'          => $book->id,
                    'nomor_inventaris' => $book->kode_buku . '-INV-' . sprintf('%03d', $i),
                    'kondisi'          => 'baik',
                    'status_pinjam'    => 'tersedia',
                ]);
            }

            return redirect()->route('book.index')->with('success', 'Buku baru dan nomor inventaris berhasil ditambahkan!');
        });
    }

    public function update(Request $request)
    {
        $book = Book::findOrFail($request->id);

        $categoryId = $request->categories_id;
        if (!is_numeric($categoryId)) {
            $category = Category::firstOrCreate(['nama' => $categoryId ?? 'Umum']);
            $categoryId = $category->id;
        }

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

        return DB::transaction(function () use ($request, $book, $categoryId) {
            $oldStok = $book->stok;
            $newStok = (int) $request->stok;

            $dataToUpdate = [
                'kode_buku'         => $request->kode_buku,
                'categories_id'     => $categoryId,
                'judul'             => $request->judul,
                'penulis'           => $request->penulis,
                'penerbit'          => $request->penerbit,
                'isbn'              => $request->isbn,
                'tanggal_pembelian' => date('Y-m-d', strtotime($request->tanggal_pembelian)),
                'tahun_terbit'      => $request->tahun_terbit,
                'stok'              => $newStok,
                'deskripsi'         => $request->deskripsi ?? null,
                'rak'               => $request->rak ?? null,
            ];

            if ($request->hasFile('cover')) {
                if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                    Storage::disk('public')->delete($book->cover);
                }
                $dataToUpdate['cover'] = $request->file('cover')->store('covers', 'public');
            }

            $book->update($dataToUpdate);

            // Sesuaikan item fisik di tabel book_items jika stok bertambah/berkurang
            if ($newStok > $oldStok) {
                // Jika stok bertambah, buat item fisik baru kelanjutan dari jumlah sebelumnya
                for ($i = $oldStok + 1; $i <= $newStok; $i++) {
                    BookItem::create([
                        'book_id'          => $book->id,
                        'nomor_inventaris' => $book->kode_buku . '-INV-' . sprintf('%03d', $i),
                        'kondisi'          => 'baik',
                        'status_pinjam'    => 'tersedia',
                    ]);
                }
            } elseif ($newStok < $oldStok) {
                // Jika stok dikurangi, hapus item fisik yang belum dipinjam dari urutan paling belakang
                $book->bookItems()
                     ->where('status_pinjam', 'tersedia')
                     ->orderBy('id', 'desc')
                     ->take($oldStok - $newStok)
                     ->delete();
            }

            return redirect()->route('book.index')->with('success', 'Data buku dan inventaris berhasil diperbarui!');
        });
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete(); // Karena menggunakan cascadeOnDelete di migrasi, book_items otomatis ikut terhapus

        return redirect()->route('book.index')->with('success', 'Buku berhasil dihapus dari database!');
    }
}