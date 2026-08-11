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

        // Filter berdasarkan Pencarian
        if ($search) {
            $searchTerm = strtolower(trim($search));

            if ($searchTerm === 'expired') {
                // Jika diketik "expired", cari user yang masa berlakunya lewat dari hari ini ATAU NULL
                $query->where(function ($q) {
                    $q->where('masa_berlaku_sampai', '<', now())
                    ->orWhereNull('masa_berlaku_sampai');
                });
            } elseif ($searchTerm === 'aktif') {
                // Jika diketik "aktif", cari user yang masa berlakunya masih aktif (>= hari ini)
                $query->where('masa_berlaku_sampai', '>=', now());
            } else {
                // Jika mengetik biasa (Nama / NISN / NIK)
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
                });
            }
        }

        // Filter berdasarkan Status (Siswa, Guru, Umum)
        if ($statusFilter && in_array($statusFilter, ['siswa', 'guru', 'umum'])) {
            $query->where('status', $statusFilter);
        }

        $students = $query->latest()->paginate(10)->withQueryString();

        // AMBIL DATA KELAS UNIK UNTUK DROPDOWN DINAMIS
        $allKelas = User::whereNotNull('kelas')
                        ->where('kelas', '!=', '')
                        ->distinct()
                        ->pluck('kelas');

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search', 'statusFilter', 'allKelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_induk'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $id,
            'status'        => 'required|in:siswa,guru,umum', 
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat'        => 'nullable|string|max:500',
            'jenjang'       => 'nullable|in:MA,MTS',
            'kelas'         => 'nullable|string|max:255',
            'kelas_baru'    => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);

        $status = strtolower($request->status); 

        $nisn = null;
        $nik = null;

        if ($status === 'siswa') {
            $nisn = $request->nomor_induk;
        } elseif ($status === 'guru' || $status === 'umum') {
            $nik = $request->nomor_induk;
        }

        $kelasFinal = $request->kelas;
        if ($request->filled('kelas_baru')) {
            $kelasFinal = $request->kelas_baru;
        }

        $user->update([
            'nisn'          => $nisn,
            'nik'           => $nik,
            'name'          => $request->name,
            'email'         => $request->email,
            'status'        => $status,   
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat'        => $request->alamat,
            'jenjang'       => $request->jenjang,
            'kelas'         => $kelasFinal,
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
            'kelas_baru'    => 'nullable|string|max:255',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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

        // Tentukan bulan cut-off 
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

        // Tentukan kelas final (jika isi input kelas_baru, gunakan itu. Jika tidak, pakai pilihan dropdown kelas)
        $kelasFinal = $request->kelas;
        if ($request->filled('kelas_baru')) {
            $kelasFinal = $request->kelas_baru;
        }

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
            'kelas'               => $kelasFinal, 
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

    public function perpanjang(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $masaMulai = Carbon::now();
        $tahunDepan = $masaMulai->year + 1;
        $masaSampai = Carbon::create($tahunDepan, 6, 30);

        $user->update([
            'masa_berlaku_mulai'  => $masaMulai->toDateString(),
            'masa_berlaku_sampai' => $masaSampai->toDateString(),
            'status_kartu'        => 'aktif',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Masa berlaku kartu berhasil diperpanjang hingga 30 Juni ' . $tahunDepan . '!'
            ]);
        }

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
        $users = User::whereIn('id', $selectedIds)->get()->map(function ($user) {
            // Format masa berlaku dari database (contoh: 30 Juni 2027)
            $user->masaBerlakuFormatted = $user->masa_berlaku_sampai 
                ? \Carbon\Carbon::parse($user->masa_berlaku_sampai)->translatedFormat('d F Y') 
                : '-';
            
            // Tentukan juga No Induk (NISN jika siswa, NIK jika guru/umum)
            $user->noInduk = $user->nisn ?? $user->nik ?? '-';

            return $user;
        });

        return view('layouts.pages.admin.print_cards', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function destroyMultiple(Request $request)
    {
        // Validasi bahwa data yang dikirim berupa array dan ID-nya ada di database
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id' 
        ]);

        // Hapus data secara massal berdasarkan array ID yang dicentang
        \App\Models\User::whereIn('id', $request->ids)->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'User yang dipilih berhasil dihapus.');
    }

    public function destroyExpired()
    {
        $deleted = User::where('role', '!=', 'admin')
            ->where(function ($query) {
                $query->where('masa_berlaku_sampai', '<', now())
                    ->orWhereNull('masa_berlaku_sampai');
            })
            ->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$deleted} user yang berstatus expired.");
    }
}