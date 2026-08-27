<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ebook; 

class UserEbookController extends Controller
{
    /**
     * Menampilkan daftar katalog e-book di sisi User 
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data e-book 
        $ebooks = Ebook::with('category')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(6)
            ->withQueryString() 
            ->fragment('katalog-ebook'); 

        return view('layouts.pages.users.ebook_users', compact('ebooks'));
    }

    /**
     * Membuka file PDF e-book secara online langsung di browser (Tanpa Download)
     */
    public function read($id)
    {
        $ebook = Ebook::findOrFail($id);
        
        $path = storage_path('app/public/' . $ebook->file_pdf); 

        if (!file_exists($path)) {
            abort(404, 'Maaf, file e-book tidak ditemukan di server.');
        }

        // Mengembalikan respons file inline agar tampil langsung di browser
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $ebook->judul . '.pdf"'
        ]);
    }
}