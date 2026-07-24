<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MemberController extends Controller
{
    public function index(Request $request)
    {
    // Menampilkan seluruh data anggota dan fitur pencarian anggota
        $search = $request->query('search');
        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'id'   => 'required|exists:users,id',
            'nis'  => 'nullable|string',
            'name' => 'required|string|max:255',
            'role' => 'required|string'
        ]);

        // Cari user berdasarkan ID
        $user = User::findOrFail($request->id);
        
        // Simpan perubahan
        $user->update([
            'nis'  => $request->nis,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->route('member.index')->with('success', 'Data user berhasil diperbarui!');
    }
}