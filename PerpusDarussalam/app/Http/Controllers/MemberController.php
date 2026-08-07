<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $statusFilter = $request->query('role'); // Menangkap filter status (siswa, guru, umum)

        $query = User::query();

        // Filter berdasarkan Pencarian (Nama / NISN / NIK) 
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan Status (Siswa, Guru, Umum)
        if ($statusFilter && in_array($statusFilter, ['siswa', 'guru', 'umum'])) {
            $query->where('status', $statusFilter);
        }

        $students = $query->latest()->paginate(10)->withQueryString();

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search', 'statusFilter'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:users,id',
            'nomor_induk'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $request->id, 
            'role'          => 'required|in:siswa,guru,umum', // Ini mengarah ke kolom status
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
            'jenjang'       => 'nullable|in:MA,MTS',
            'kelas'         => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->id);

        $status = strtolower($request->role); // Dari form add/edit menampung value status di select name="role"
        
        $nisn = null;
        $nik = null;

        // Isi hanya pada variabel yang sesuai dengan status yang dipilih
        if ($status === 'siswa') {
            $nisn = $request->nomor_induk;
        } elseif ($status === 'guru' || $status === 'umum') {
            $nik = $request->nomor_induk;
        }

        // Simpan ke database 
        $user->update([
            'nisn'          => $nisn,
            'nik'           => $nik,
            'name'          => $request->name,
            'email'         => $request->email,
            'status'        => $status, // Masuk ke kolom status di database
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat'        => $request->alamat,
            'jenjang'       => $request->jenjang,
            'kelas'         => $request->kelas,
        ]);

        return redirect()->route('member.index')->with('success', 'Data user berhasil diperbarui!');
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('addUserForm', [
            'nomor_induk'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:siswa,guru,umum',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
            'jenjang'       => 'nullable|in:MA,MTS',
            'kelas'         => 'nullable|string|max:255',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Proses penyimpanan file foto jika diunggah
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto-user', 'public');
        }

        // Tentukan kolom nomor induk berdasarkan role/status
        $role = strtolower($request->role);
        $nisn = null;
        $nik = null;

        if ($role === 'siswa') {
            $nisn = $request->nomor_induk;
        } elseif ($role === 'guru' || $role === 'umum') {
            $nik = $request->nomor_induk;
        }

        $status = $role;

        // Tentukan tanggal mulai (hari ini)
        $masaMulai = Carbon::now(); 

        // Tentukan bulan cut-off tahun ajaran baru (misal bulan Juni = bulan ke-6)
        $tahunSekarang = $masaMulai->year;
        $bulanSekarang = $masaMulai->month;

        // Jika daftar sebelum atau pas bulan Juni, expired-nya Juni tahun ini. 
        // Tapi kalau daftar setelah Juni (misal Juli - Desember), expired-nya Juni tahun depan.
        if ($bulanSekarang <= 6) {
            $masaSampai = Carbon::create($tahunSekarang, 6, 30); // 30 Juni tahun ini
        } else {
            $masaSampai = Carbon::create($tahunSekarang + 1, 6, 30); // 30 Juni tahun depan
        }

        // Cek apakah sudah expired berdasarkan tanggal hari ini
        $statusKartu = Carbon::now()->lte($masaSampai) ? 'aktif' : 'expired';

        // Proses simpan ke database
        User::create([
            'nisn'                => $nisn,
            'nik'                 => $nik,
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => bcrypt($request->password), 
            'status'              => $status,
            'role'                => 'user',
            'jenis_kelamin'       => $request->jenis_kelamin,
            'alamat'              => $request->alamat,
            'jenjang'             => $request->jenjang ?? 'MTS',
            'kelas'               => $request->kelas,
            'masa_berlaku_mulai'  => $masaMulai->toDateString(),
            'masa_berlaku_sampai' => $masaSampai->toDateString(),
            'status_kartu'        => $statusKartu,
            'foto'                => $pathFoto,
        ]);

        // Cek jika request berasal dari AJAX (Fetch/Axios)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User baru berhasil ditambahkan!'
            ]);
        }

        return redirect()->route('member.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function perpanjang($id)
    {
        $user = User::findOrFail($id);

        // Tanggal mulai perpanjangan dihitung hari ini
        $masaMulai = Carbon::now();
        
        // Masa berlaku sampai diatur ke tanggal 30 Juni tahun depan
        $tahunDepan = $masaMulai->year + 1;
        $masaSampai = Carbon::create($tahunDepan, 6, 30); // 30 Juni tahun depan

        $user->update([
            'masa_berlaku_mulai'  => $masaMulai->toDateString(),
            'masa_berlaku_sampai' => $masaSampai->toDateString(),
            'status_kartu'        => 'aktif', // Otomatis aktif kembali
        ]);

        return redirect()->back()->with('success', 'Masa berlaku kartu berhasil diperpanjang hingga 30 Juni ' . $tahunDepan . '!');
    }

    public function scanBarcode(Request $request)
    {
        // Cari user berdasarkan barcode / NISN / NIK / ID yang diset di kartu
        $user = User::where('nisn', $request->barcode)
                    ->orWhere('nik', $request->barcode)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data user atau kartu tidak ditemukan!'
            ], 404);
        }

        // CEK APAKAH KARTU SUDAH EXPIRED
        // Menggunakan accessor / pengecekan tanggal masa_berlaku_sampai
        if ($user->status_kartu === 'expired') {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Kartu anggota telah kedaluwarsa (Expired). Silakan perbarui masa berlaku kartu.'
            ], 400);
        }

        // Jika aktif, proses absensi atau data dilanjutkan di sini...
        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil, kartu aktif!',
            'data' => $user
        ]);
    }

    public function printCards(Request $request)
    {
        // Ambil ID user yang dicentang dari form modal
        $selectedIds = $request->input('selected_users', []);

        // Validasi tambahan di backend 
        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kartu anggota yang ingin dicetak terlebih dahulu!');
        }

        // Ambil data user dari database berdasarkan ID yang dipilih
        $users = User::whereIn('id', $selectedIds)->get();

        return view('layouts.pages.admin.print_cards', compact('users'));
    }
}