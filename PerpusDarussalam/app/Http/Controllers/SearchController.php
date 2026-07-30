<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\Category;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        /*1. Untuk mengambil teks kata kunci yang diketik oleh user di kolom pencarian. */
        $query = $request->input('q');

        /*2. Langsung kembalikan halaman dengan daftar buku kosong, 
        tapi tetap mengirimkan semua data kategori untuk kebutuhan sidebar/filter. */
        if (empty($query)) {
            return view('layouts.search-result', [
                'books' => collect([]),
                'categories' => Category::all()
            ]);
        }
        /* 3. Jika ada kata kunci, lakukan pencarian di database.*/
        $books = Book::query()->where(function($keywordQuery) use ($query) {
            $keywordQuery->where('judul', "%{$query}%")
                         ->orWhere('penerbit', "%{$query}%");
        })->get();

        /* 4. Kembalikan halaman hasil pencarian dengan daftar buku hasil pencarian.*/
        return view('layouts.search-result', [
            'books' => $books,
            'categories' => Category::all()
        ]);
    }
}