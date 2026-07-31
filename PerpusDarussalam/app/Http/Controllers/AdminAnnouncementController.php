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

        // Nonaktifkan pengumuman lama atau langsung buat/perbarui baris aktif
        Announcement::query()->update(['is_active' => false]);
        
        Announcement::create([
            'content' => $request->content,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Pengumuman teks berjalan berhasil diperbarui!');
    }
}
