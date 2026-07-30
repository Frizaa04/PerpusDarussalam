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
        'email'         => 'required|email|max:255|unique:users,email,' . $request->id, 
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

    public function store(Request $request)
    {
        // Validasi input termasuk foto
        $request->validate([
            'nis'           => 'nullable|string|max:255',
            'nik'           => 'nullable|string|max:255',
            'nip'           => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:siswa,guru,umum',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tambahkan validasi foto di sini
        ]);

        // Proses penyimpanan file foto jika diunggah
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            // Disimpan ke storage/app/public/foto-user
            $pathFoto = $request->file('foto')->store('foto-user', 'public');
        }

        // Simpan data ke database termasuk path foto
        User::create([
            'nis'           => $request->nis,
            'nik'           => $request->nik,
            'nip'           => $request->nip,
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password), 
            'role'          => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat'        => $request->alamat,
            'foto'          => $pathFoto, // Masukkan path foto ke database
        ]);

        return redirect()->route('member.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function printCards(Request $request)
    {
        // Ambil ID user yang dicentang dari form
        $selectedIds = $request->input('selected_users', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu user untuk dicetak kartunya.');
        }

        // Ambil data user dari database berdasarkan ID yang dipilih
        $users = User::whereIn('id', $selectedIds)->get();

        return view('layouts.pages.admin.print_cards', compact('users'));
    }

}