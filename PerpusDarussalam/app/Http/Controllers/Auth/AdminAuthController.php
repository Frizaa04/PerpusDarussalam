<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthController extends Controller
{
    // Menampilkan halaman login admin
    public function showLoginForm()
    {
        return view('layouts.pages.admin.login_admin');
    }

    // Proses login admin menggunakan tabel users tunggal dengan filter role admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek apakah user dengan email tersebut ada dan rolenya adalah admin
        $user = User::where('email', $request->email)->first();

        if ($user && $user->role !== 'admin') {
            return back()->withErrors([
                'email' => 'Akun Anda tidak memiliki hak akses sebagai administrator.',
            ])->onlyInput('email');
        }

        // Lakukan autentikasi menggunakan guard 'web'
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, Administrator!');
        }

        return back()->withErrors([
            'email' => 'Email atau password admin salah.',
        ])->onlyInput('email');
    }

    // Proses logout admin
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah keluar dari sesi Admin.');
    }
}