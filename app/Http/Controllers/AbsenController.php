<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visits;
use App\Models\User;
use Carbon\Carbon;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil filter status dari query string (jika ada)
        $status = $request->input('status');

        $visits = Visits::with('user')
            ->when($status, function ($query, $status) {
                // Menyaring kunjungan berdasarkan relasi user yang memiliki status tertentu
                return $query->whereHas('user', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->latest('visited_at')
            ->paginate(10)
            ->appends(['status' => $status]); // Agar parameter filter tetap ada saat berpindah halaman paginasi

        return view('layouts.pages.admin.absen', compact('visits', 'status'));
    }

    // Memproses barcode yang di-scan
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
        ]);

        // Mencari user berdasarkan NIS atau NIK 
        $user = User::where('nisn', $request->kode)
                    ->orWhere('nik', $request->kode)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Identitas tidak ditemukan di database!');
        }

        // Cek status kartu 
        if ($user->status_kartu === 'expired') {
            return back()->with('error', 'Absen gagal: kartu ' . $user->name . ' sudah kedaluwarsa. Silakan perpanjang kartu terlebih dahulu.');
        }

        // Cek apakah user sudah pernah melakukan absen pada hari ini
        $sudahAbsen = Visits::where('user_id', $user->id)
            ->whereDate('visited_at', Carbon::today())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Peringatan: ' . $user->name . ' sudah melakukan absen hari ini!');
        }

        // Menyimpan data kunjungan ke database jika belum pernah absen hari ini
        Visits::create([
            'user_id' => $user->id,
            'visited_at' => now(),
        ]);

        return back()->with('success', 'Absen berhasil: ' . $user->name);
    }
}