<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookItem;
use App\Models\Book;

class BookItemController extends Controller
{
    // Mengambil data item berdasarkan ID Buku (digunakan untuk AJAX di modal)
    public function getItems($id)
    {
        $items = BookItem::where('book_id', $id)->get();
        return response()->json($items);
    }

    // Menyimpan item / eksemplar baru
    public function store(Request $request)
    {
        $request->validate([
            'book_id'          => 'required|exists:books,id',
            'nomor_inventaris' => 'required|string|unique:book_items,nomor_inventaris|max:255',
            'kondisi'          => 'required|in:baik,rusak_ringan,rusak_berat',
            'status_pinjam'    => 'required|in:tersedia,dipinjam',
        ]);

        // 1. Simpan item buku baru
        BookItem::create([
            'book_id'          => $request->book_id,
            'nomor_inventaris' => $request->nomor_inventaris,
            'kondisi'          => $request->kondisi,
            'status_pinjam'    => $request->status_pinjam,
        ]);

        // 2. Tambah jumlah stok di tabel books induk secara otomatis (+1)
        $book = Book::find($request->book_id);
        $book->increment('stok');

        return redirect()->back()->with('success', 'Eksemplar buku berhasil ditambahkan!');
    }

    // Mengupdate data item / eksemplar
    public function update(Request $request, $id)
    {
        $item = BookItem::findOrFail($id);

        $request->validate([
            'nomor_inventaris' => 'required|string|max:255|unique:book_items,nomor_inventaris,' . $item->id,
            'kondisi'          => 'required|in:baik,rusak_ringan,rusak_berat',
            'status_pinjam'    => 'required|in:tersedia,dipinjam',
        ]);

        $item->update([
            'nomor_inventaris' => $request->nomor_inventaris,
            'kondisi'          => $request->kondisi,
            'status_pinjam'    => $request->status_pinjam,
        ]);

        return redirect()->back()->with('success', 'Data eksemplar berhasil diperbarui!');
    }

    // Menghapus item / eksemplar
    public function destroy($id)
    {
        $item = BookItem::findOrFail($id);
        $bookId = $item->book_id;
        
        // 1. Hapus item
        $item->delete();

        // 2. Kurangi jumlah stok di tabel books induk secara otomatis (-1)
        $book = Book::find($bookId);
        if ($book && $book->stok > 0) {
            $book->decrement('stok');
        }

        return redirect()->back()->with('success', 'Eksemplar buku berhasil dihapus!');
    }

    public function printAllBarcodes($id)
    {
        $book = Book::with('bookItems')->findOrFail($id);

        return view('layouts.pages.admin.print_all_barcodes', compact('book'));
    }

    public function printBarcode($id)
    {
        $item = BookItem::with('book')->findOrFail($id);        
        $book = $item->book;
        $book->setRelation('bookItems', collect([$item]));

        return view('layouts.pages.admin.print_all_barcodes', compact('book'));
    }
}