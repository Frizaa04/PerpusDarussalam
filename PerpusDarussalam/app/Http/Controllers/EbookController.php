<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ebook;
use App\Models\Category; // 1. Import Model Category
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    public function index()
    {
        $ebooks = Ebook::all();
        $categories = Category::all(); // 2. Ambil semua data kategori

        return view('layouts.pages.admin.ebook', compact('ebooks', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id', // 3. Validasi ID kategori
            'tahun' => 'required|digits:4',
            'file_pdf' => 'required|mimes:pdf|max:10000',
        ]);

        $filePath = $request->file('file_pdf')->store('ebooks_pdf', 'public');

        Ebook::create([
            'categories_id' => $request->categories_id, // Simpan ID kategori yang dipilih
            'kode_ebook' => 'EB-' . rand(1000, 9999),
            'judul' => $request->judul,
            'penulis' => 'Admin', 
            'penerbit' => 'Darussalam', 
            'tahun_terbit' => $request->tahun,
            'file_pdf' => $filePath,
        ]);

        return redirect()->back()->with('success', 'E-Book berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $ebook = Ebook::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'tahun' => 'required|digits:4',
            'file_pdf' => 'nullable|mimes:pdf|max:10000',
        ]);

        $data = [
            'judul' => $request->judul,
            'categories_id' => $request->categories_id,
            'tahun_terbit' => $request->tahun,
        ];

        if ($request->hasFile('file_pdf')) {
            if ($ebook->file_pdf && Storage::disk('public')->exists($ebook->file_pdf)) {
                Storage::disk('public')->delete($ebook->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')->store('ebooks_pdf', 'public');
        }

        $ebook->update($data);

        return redirect()->back()->with('success', 'Data E-Book berhasil diperbarui!');
    }
}
