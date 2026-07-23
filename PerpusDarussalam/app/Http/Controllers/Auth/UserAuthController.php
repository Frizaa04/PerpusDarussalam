<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    // Menampilkan halaman form login user
    public function showLoginForm()
    {
        return view('layouts.pages.users.login_users');
    }

    // Proses autentikasi/login
    public function login(Request $request)
    {
        // Validasi input (bisa menggunakan Email atau NIS/NIP/NIK)
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect ke halaman welcome/utama perpustakaan setelah berhasil login
            return redirect()->route('welcome')->with('success', 'Selamat datang, ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses Logout User
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('success', 'Anda telah keluar.');
    }
}