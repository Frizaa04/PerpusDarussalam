<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Menyimpan banner baru
    public function store(Request $request)
    {
        $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:51200',
        ], [
            'image.max' => 'Ukuran file gambar terlalu besar! Maksimal 50MB.',
            'image.image' => 'File yang diupload harus berupa gambar.',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');

            Banner::create([
                'image' => $imagePath,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Banner berhasil diupload.');
    }

    // Mengaktifkan banner
    public function activate($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Banner diaktifkan.');
    }

    // Menonaktifkan banner
    public function deactivate($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Banner dinonaktifkan.');
    }

    // Menghapus banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Hapus file fisik dari storage
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus.');
    }
}
