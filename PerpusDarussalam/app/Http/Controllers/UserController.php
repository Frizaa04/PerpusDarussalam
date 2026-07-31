<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('layouts.pages.users.home');
    }

    public function store(Request $request)
    {
        // 1. TAHAP VALIDASI (Pintu Utama)
        // Jika input tidak memenuhi syarat di bawah, Laravel otomatis membatalkan proses
        // dan mengembalikan error ke tampilan form tanpa menyimpan apa pun.
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB, harus format gambar
        ]);

        // 2. TAHAP PENYIMPANAN FILE (Hanya berjalan jika lolos validasi)
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            // Disimpan ke storage/app/public/foto-user
            $pathFoto = $request->file('foto')->store('foto-user', 'public');
        }

        // 3. TAHAP INSERT KE DATABASE
        User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'foto'  => $pathFoto, // Path "foto-user/namafile.jpg" disimpan di DB
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
}