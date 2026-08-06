<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book; // Import Model Book
use Illuminate\Http\Request;

class UserController extends Controller
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
                ->orWhere('isbn', 'LIKE', "%{$search}%");
            });
        }

        // ->withQueryString() agar parameter pencarian tetap ikut saat berpindah halaman
        $books = $queryBuilder->latest()->paginate(6)->withQueryString()->fragment('katalog-buku');

        return view('layouts.pages.users.home', compact('books'));
    }

    public function store(Request $request)
    {
        // 1. TAHAP VALIDASI
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. TAHAP PENYIMPANAN FILE
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto-user', 'public');
        }

        // 3. TAHAP INSERT KE DATABASE
        User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'foto'  => $pathFoto,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    public function showBookDetail($id)
    {
        $book = Book::with('categories')->findOrFail($id);
        
        return view('layouts.pages.users.book_detail', compact('book'));
    }
}