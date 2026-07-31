<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('layouts.pages.users.login_users');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginInput = $request->input('username');
        $password = $request->input('password');

        // Cari user yang cocok berdasarkan email, nis, nik, atau username
        // Cari user yang cocok berdasarkan email, nis, atau nik
        $user = User::where('email', $loginInput)
            ->orWhere('nis', $loginInput)
            ->orWhere('nik', $loginInput)
            ->first();

        // Jika user ditemukan dan pencocokan password berhasil
        if ($user && Auth::guard('web')->attempt(['email' => $user->email, 'password' => $password])) {
            $request->session()->regenerate();
            
            // Hapus jejak redirect bekas session admin
            $request->session()->forget('url.intended');

            return redirect()->route('user.home');
        }

        return back()->withErrors([
            'username' => 'Kredensial yang dimasukkan tidak sesuai.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}