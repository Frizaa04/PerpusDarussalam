<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;


class BookBarcodeController extends Controller
{
    public function storeBuku(Request $request)
    // Membaca barcode dari buku 
    {
        $request->validate([
            'categories_id' => 'required|exists:categories,id', 
            'kode_buku'     => 'required|string|unique:books,kode_buku', 
            'judul'         => 'required|string',
            'penulis'       => 'required|string',
            'penerbit'      => 'required|string',
            'tahun_terbit'  => 'required|digits:4',
            'isbn'          => 'required|string',
            'tanggal_pembelian' => 'required|date',
            'stok'          => 'integer|min:0',
            'cover'         => 'nullable|string', 
        ]);

        $book = Book::create([
            'categories_id' => $request->categories_id,
            'kode_buku'     => $request->kode_buku,
            'judul'         => $request->judul,
            'penulis'       => $request->penulis,
            'penerbit'      => $request->penerbit,
            'tahun_terbit'  => $request->tahun_terbit,
            'isbn'          => $request->isbn,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'stok'          => $request->stok ?? 0,
            'cover'         => $request->cover,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil didaftarkan',
            'data'    => $book
        ], 201);
    }
}