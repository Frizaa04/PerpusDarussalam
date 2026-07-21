<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search'));
    }

    public function update(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'id'   => 'required|exists:users,id',
            'nis'  => 'required|string',
            'name' => 'required|string|max:255',
            'role' => 'required|string'
        ]);

        // 2. Cari user berdasarkan ID (karena NIS sekarang bisa diubah)
        $user = User::findOrFail($request->id);
        
        // 3. Simpan perubahan (NIS, Name, dan Role)
        $user->update([
            'nis'  => $request->nis,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->route('member.index')->with('success', 'Data user berhasil diperbarui!');
    }
}