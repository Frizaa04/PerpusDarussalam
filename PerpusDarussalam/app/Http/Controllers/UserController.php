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
            $books = $queryBuilder->where(function ($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                ->orWhere('kode_buku', 'LIKE', "%{$search}%")
                ->orWhere('penulis', 'LIKE', "%{$search}%")
                ->orWhere('penerbit', 'LIKE', "%{$search}%")
                ->orWhere('isbn', 'LIKE', "%{$search}%");
            })->latest()->get();
        } else {
            // Tampilkan tepat 6 BUKU TERBARU agar pas memenuhi 1 baris
            $books = $queryBuilder->latest()->take(6)->get();
        }

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
}