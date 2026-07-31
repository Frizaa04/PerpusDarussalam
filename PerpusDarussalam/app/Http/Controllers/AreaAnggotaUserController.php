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

        // 2. Ambil riwayat peminjaman user beserta detail bukunya dari bookItem
        $peminjamans = Borrowing::with('bookItem.book') // Mengambil item buku beserta master data bukunya
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('layouts.pages.users.area_anggota', compact('user', 'peminjamans'));
    }
}