<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

    public function updatePassword(Request $request)
    {
        // 1. Matikan semua aturan rumit, buat seminimal mungkin dulu untuk tes
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 6 karakter!',
            'password.confirmed' => 'Password baru tidak cocok!',
        ]);

        $userId = auth()->guard('web')->id();
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return back()->with('error', 'Sesi tidak ditemukan.');
        }

        // 2. Jika lolos pengecekan, eksekusi ganti password
        $user->password = $request->password;
        $user->save();

        // 3. Kick Session
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password berhasil diubah!');
    }
}