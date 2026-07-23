<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Menampilkan halaman login admin
    public function showLoginForm()
    {
        return view('layouts.pages.admin.login_admin');
    }

    // Proses login admin menggunakan guard 'admin'
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
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
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah keluar dari sesi Admin.');
    }
}