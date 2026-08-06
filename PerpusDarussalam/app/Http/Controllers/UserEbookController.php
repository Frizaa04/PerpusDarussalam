<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ebook; 

class UserEbookController extends Controller
{
    /**
     * Menampilkan daftar katalog e-book di sisi User (Pemustaka)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data e-book dengan fitur pencarian yang dibungkus rapi
        $ebooks = Ebook::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%"); // Opsional: tambah penerbit jika ada
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Mempertahankan parameter URL saat ganti halaman pagination

        return view('layouts.pages.users.ebook_users', compact('ebooks'));
    }

    /**
     * Membuka file PDF e-book secara online langsung di browser (Tanpa Download)
     */
    public function read($id)
    {
        $ebook = Ebook::findOrFail($id);
        
        // Sesuaikan kolom database penyimpanan file PDF Anda (misal: $ebook->file_pdf atau $ebook->file)
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