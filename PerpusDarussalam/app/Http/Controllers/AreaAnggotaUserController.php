<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;

class AreaAnggotaUserController extends Controller
{
    public function index()
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Proteksi jika user belum login, redirect ke route user.login
        if (!$user) {
            return redirect()->route('user.login');
        }

        // 3. Ambil riwayat peminjaman dengan PAGINASI (10 data per halaman)
        $peminjamans = Borrowing::with('bookItem.book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10); // <-- Diubah di sini!

        return view('layouts.pages.users.area_anggota', compact('user', 'peminjamans'));
    }
}