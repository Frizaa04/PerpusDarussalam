<?php

namespace App\Http\Controllers; // Sesuaikan jika menggunakan subfolder (\User)

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing; // Gunakan Model Borrowing

class AreaAnggotaUserController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Proteksi jika user belum login, redirect ke route user.login
        if (!$user) {
            return redirect()->route('user.login'); // <-- Diubah di sini!
        }

        // 3. Ambil riwayat peminjaman beserta relasi buku
        $peminjamans = Borrowing::with('bookItem.book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('layouts.pages.users.area_anggota', compact('user', 'peminjamans'));
    }
}