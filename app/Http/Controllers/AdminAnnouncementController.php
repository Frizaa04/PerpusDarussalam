<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AdminAnnouncementController extends Controller
{
    // Menyimpan atau memperbarui pengumuman teks berjalan
public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Nonaktifkan semua pengumuman yang sedang aktif
        Announcement::query()->update(['is_active' => false]);
        
        // Buat baru dan langsung aktifkan
        Announcement::create([
            'content' => $request->content,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Pengumuman teks berjalan berhasil ditambahkan dan diaktifkan!');
    }

    // Mengaktifkan kembali pengumuman dari daftar riwayat
    public function activate($id)
    {
        // Nonaktifkan semua
        Announcement::query()->update(['is_active' => false]);

        // Aktifkan yang dipilih berdasarkan ID
        Announcement::where('id', $id)->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Pengumuman berhasil diaktifkan kembali!');
    }

    // Menghapus pengumuman dari database
    public function destroy($id)
    {
        Announcement::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus permanen dari riwayat!');
    }
}
