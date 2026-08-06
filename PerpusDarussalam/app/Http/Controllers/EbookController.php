<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ebook;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $ebooks = Ebook::with('category') // <-- Tambahkan with('category') di sini
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('kode_ebook', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::all();
        return view('layouts.pages.admin.ebook', compact('ebooks', 'categories', 'search'));
    }

    public function store(Request $request)
    {
        // Menggunakan validateWithBag dengan error bag 'ebookStoreForm'
        $request->validateWithBag('ebookStoreForm', [
            'kode_ebook' => 'required|string|unique:ebooks,kode_ebook|max:255',
            'judul' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id', 
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4',
            'isbn' => 'nullable|string|max:255',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png|max:5000',
            'file_pdf' => 'required|file|mimes:pdf|max:20000',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('ebooks_cover', 'public');
        }

        $filePath = $request->file('file_pdf')->store('ebooks_pdf', 'public');

        Ebook::create([
            'categories_id' => $request->categories_id, 
            'kode_ebook' => $request->kode_ebook,
            'judul' => $request->judul,
            'penulis' => $request->penulis, 
            'penerbit' => $request->penerbit, 
            'tahun_terbit' => $request->tahun_terbit,
            'isbn' => $request->isbn,
            'cover' => $coverPath,
            'file_pdf' => $filePath,
        ]);

        return redirect()->back()->with('success', 'E-Book berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $ebook = Ebook::findOrFail($id);

        // Menggunakan validateWithBag dengan error bag 'ebookUpdateForm'
        $request->validateWithBag('ebookUpdateForm', [
            'kode_ebook' => 'required|string|max:255|unique:ebooks,kode_ebook,' . $id,
            'judul' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4',
            'isbn' => 'nullable|string|max:255',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'file_pdf' => 'nullable|mimes:pdf|max:10000',
        ]);

        $data = [
            'kode_ebook' => $request->kode_ebook,
            'judul' => $request->judul,
            'categories_id' => $request->categories_id,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'isbn' => $request->isbn,
        ];

        // Replace Cover
        if ($request->hasFile('cover')) {
            if ($ebook->cover && Storage::disk('public')->exists($ebook->cover)) {
                Storage::disk('public')->delete($ebook->cover);
            }
            $data['cover'] = $request->file('cover')->store('ebooks_cover', 'public');
        }

        // Replace PDF File
        if ($request->hasFile('file_pdf')) {
            if ($ebook->file_pdf && Storage::disk('public')->exists($ebook->file_pdf)) {
                Storage::disk('public')->delete($ebook->file_pdf);
            }
            $data['file_pdf'] = $request->file('file_pdf')->store('ebooks_pdf', 'public');
        }

        $ebook->update($data);

        return redirect()->back()->with('success', 'Data E-Book berhasil diperbarui!');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids) {
            return redirect()->back()->with('error', 'Pilih minimal satu e-book yang ingin dihapus!');
        }

        $ebooks = Ebook::whereIn('id', $ids)->get();

        foreach ($ebooks as $ebook) {
            if ($ebook->cover && Storage::disk('public')->exists($ebook->cover)) {
                Storage::disk('public')->delete($ebook->cover);
            }
            $ebooks_pdf_path = $ebook->file_pdf;
            if ($ebooks_pdf_path && Storage::disk('public')->exists($ebooks_pdf_path)) {
                Storage::disk('public')->delete($ebooks_pdf_path);
            }
            $ebook->delete();
        }

        return redirect()->back()->with('success', 'E-book yang dipilih berhasil dihapus!');
    }
}