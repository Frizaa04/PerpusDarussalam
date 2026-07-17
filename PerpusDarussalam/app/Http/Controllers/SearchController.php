<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\categories;
use App\Models\Book;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return view('layouts.search-result', [
                'books' => collect([]),
                'categories' => categories::all()
            ]);
        }

        $books = Book::query()->where(function($keywordQuery) use ($query) {
            $keywordQuery->where('judul', "%{$query}%")
                         ->orWhere('penerbit', "%{$query}%");
        })->get();

        return view('layouts.search-result', [
            'books' => $books,
            'categories' => categories::all()
        ]);
    }
}