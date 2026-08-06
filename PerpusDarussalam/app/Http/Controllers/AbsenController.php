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
        // Mengambil filter role dari query string (jika ada)
        $role = $request->input('role');

        $visits = Visits::with('user')
            ->when($role, function ($query, $role) {
                // Menyaring kunjungan berdasarkan relasi user yang memiliki role tertentu
                return $query->whereHas('user', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            })
            ->latest('visited_at')
            ->paginate(10)
            ->appends(['role' => $role]); // Agar parameter filter tetap ada saat berpindah halaman paginasi

        return view('layouts.pages.admin.absen', compact('visits', 'role'));
    }

    // Memproses barcode yang di-scan
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
        ]);

        // Mencari user berdasarkan NIS atau NIK 
        $user = User::where('nis', $request->kode)
                    ->orWhere('nik', $request->kode)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Identitas tidak ditemukan di database!');
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