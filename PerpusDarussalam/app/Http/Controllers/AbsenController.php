<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visits;
use App\Models\User;

class AbsenController extends Controller
{
    public function index()
    {
        // Mengambil data visits beserta relasi user
        // Diurutkan dari kunjungan terbaru
        $visits = Visits::with('user')
            ->latest('visited_at')
            ->get();

        return view('layouts.pages.admin.absen', compact('visits'));
    }

    // Tambahkan method ini untuk memproses barcode yang di-scan
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
        ]);

        // Mencari user berdasarkan NIS, NIP, atau NIK
        $user = User::where('nis', $request->kode)
                    ->orWhere('nip', $request->kode)
                    ->orWhere('nik', $request->kode)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Identitas tidak ditemukan di database!');
        }

        // Menyimpan data kunjungan ke database
        Visits::create([
            'user_id' => $user->id,
            'visited_at' => now(),
        ]);

        return back()->with('success', 'Absen berhasil: ' . $user->name);
    }
}