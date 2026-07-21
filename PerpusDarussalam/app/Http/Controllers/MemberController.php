<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Pastikan Model User di-import

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Mulai query dari Model User database
        $query = User::query();

        // Logika filter pencarian berdasarkan nama atau NIS
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        // Ambil data dari database
        $students = $query->get();

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search'));
    }

    public function update(Request $request, string $id)
    {
        // 1. Validasi input
        $request->validate([
            'nis'  => 'required|string',
            'name' => 'required|string|max:255',
            'role' => 'required|string'
        ]);

        // 2. Cari user berdasarkan ID, lalu perbarui datanya
        $user = User::findOrFail($id);
        
        $user->update([
            'nis'  => $request->nis,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->route('member.index')->with('success', 'Data user berhasil diperbarui!');
    }
}