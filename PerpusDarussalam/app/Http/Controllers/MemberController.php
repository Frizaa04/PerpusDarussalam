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

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search', 'roleFilter'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:users,id',
            'nomor_induk'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $request->id, 
            'role'          => 'required|in:siswa,guru,umum',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->id);

        $role = strtolower($request->role);
        
        // 1. Kosongkan ketiganya terlebih dahulu untuk mencegah bentrok data lama
        $nis = null;
        $nip = null;
        $nik = null;

        // 2. Isi hanya pada variabel yang sesuai dengan role yang dipilih saat ini
        if ($role === 'siswa') {
            $nis = $request->nomor_induk;
        } elseif ($role === 'guru') {
            $nip = $request->nomor_induk;
        } elseif ($role === 'umum') {
            $nik = $request->nomor_induk;
        }

        // 3. Simpan ke database
        $user->update([
            'nis'           => $nis,
            'nik'           => $nik,
            'nip'           => $nip,
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
        // 1. Ganti validasi biasa menjadi validateWithBag khusus 'addUserForm'
        $validated = $request->validateWithBag('addUserForm', [
            'nomor_induk'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:siswa,guru,umum',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Proses penyimpanan file foto jika diunggah
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto-user', 'public');
        }

        // 3. Tentukan kolom nomor induk berdasarkan role yang dipilih
        $role = strtolower($request->role);
        $nis = null;
        $nip = null;
        $nik = null;

        if ($role === 'siswa') {
            $nis = $request->nomor_induk;
        } elseif ($role === 'guru') {
            $nip = $request->nomor_induk;
        } elseif ($role === 'umum') {
            $nik = $request->nomor_induk;
        }

        // 4. Simpan data ke database
        User::create([
            'nis'           => $nis,
            'nik'           => $nik,
            'nip'           => $nip,
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password), 
            'role'          => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat'        => $request->alamat,
            'foto'          => $pathFoto,
        ]);

        // 5. Cek jika request berasal dari AJAX (Fetch/Axios)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User baru berhasil ditambahkan!'
            ]);
        }

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