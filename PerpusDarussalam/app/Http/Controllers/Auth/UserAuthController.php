<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        // Jalur disesuaikan dengan folder layouts/pages/users/login_users.blade.php
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

        // Deteksi apakah input berupa email atau field kredensial lainnya (nis / nik / username)
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password'  => $password,
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('user.home'));
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

        return redirect()->route('user.login');
    }
}