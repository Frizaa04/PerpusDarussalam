<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MemberController extends Controller
{
    public function index(Request $request)
{
    $search = $request->query('search');
    $roleFilter = $request->query('role'); // Menangkap filter peran (siswa, guru, umum)

    $query = User::query();

    // Filter berdasarkan Pencarian (Nama / NIS / NIP / NIK)
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('nis', 'LIKE', "%{$search}%")
              ->orWhere('nip', 'LIKE', "%{$search}%")
              ->orWhere('nik', 'LIKE', "%{$search}%");
        });
    }

    // Filter berdasarkan Role / Peran
    if ($roleFilter && in_array($roleFilter, ['siswa', 'guru', 'umum'])) {
        $query->where('role', $roleFilter);
    }

    $students = $query->latest()->paginate(10)->withQueryString();

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search'));
    }

    public function update(Request $request)
{
    // Validasi input sesuai dengan data lengkap di form edit
    $request->validate([
        'id'            => 'required|exists:users,id',
        'nis'           => 'nullable|string|max:255',
        'nik'           => 'nullable|string|max:255',
        'nip'           => 'nullable|string|max:255',
        'name'          => 'required|string|max:255',
        'email'         => 'required|email|max:255|unique:users,email,' . $request->id, // Mengabaikan email milik user itu sendiri saat validasi unique
        'role'          => 'required|in:siswa,guru,umum',
        'jenis_kelamin' => 'nullable|in:L,P',
        'alamat'        => 'nullable|string|max:500',
    ]);

    // Cari user berdasarkan ID
    $user = User::findOrFail($request->id);
    
    // Simpan perubahan ke database
    $user->update([
        'nis'           => $request->nis,
        'nik'           => $request->nik,
        'nip'           => $request->nip,
        'name'          => $request->name,
        'email'         => $request->email,
        'role'          => $request->role,
        'jenis_kelamin' => $request->jenis_kelamin,
        'alamat'        => $request->alamat,
    ]);

    return redirect()->route('member.index')->with('success', 'Data user berhasil diperbarui!');
}
}