<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Transaction::with('user');

        // Fitur Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jenis', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  // HAPUS BARIS INI: ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan tanggal terbaru & gunakan paginasi (10 data per halaman)
        $transactions = $query->orderBy('id', 'desc')->paginate(10);

        // Pertahankan parameter search saat ganti halaman paginasi
        $transactions->appends($request->all());

        return view('layouts.pages.admin.transaksi', compact('transactions', 'search'));
    }

    /**
     * Menyimpan data transaksi baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'no_identitas' => 'nullable|string|max:255',
            'nominal'      => 'required|numeric|min:0',
            'jenis'        => 'required|in:pembuatan_kartu,kehilangan_kartu,denda_keterlambatan',
            'tanggal'      => 'required|date',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $userId = null;
        $namaUser = 'Non-Anggota'; // Default jika tidak ditemukan

        // Jika No Identitas diisi, cari user yang cocok di database
        if ($request->filled('no_identitas')) {
            $identitas = trim($request->no_identitas);

            $user = User::where(function($query) use ($identitas) {
                $query->where('id', $identitas)
                      ->orWhere('email', $identitas)
                      ->orWhere('nis', $identitas)
                      ->orWhere('nip', $identitas)
                      ->orWhere('nik', $identitas);
            })->first();

            if ($user) {
                $userId = $user->id;
                $namaUser = $user->name; // Ambil nama asli dari tabel user
            }
        }

        // Simpan Data Transaksi
        Transaction::create([
            'user_id'    => $userId,
            'name'       => $namaUser,
            'jenis'      => $request->jenis,
            'nominal'    => $request->nominal,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * Memperbarui data transaksi di database.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Validasi Input
        $request->validate([
            'no_identitas' => 'nullable|string|max:255',
            'nominal'      => 'required|numeric|min:0',
            'jenis'        => 'required|in:pembuatan_kartu,kehilangan_kartu,denda_keterlambatan',
            'tanggal'      => 'required|date',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $userId = null;
        $namaUser = 'Non-Anggota'; // Default jika tidak ditemukan

        if ($request->filled('no_identitas')) {
            $identitas = trim($request->no_identitas);

            $user = User::where(function($query) use ($identitas) {
                $query->where('id', $identitas)
                      ->orWhere('email', $identitas)
                      ->orWhere('nis', $identitas)
                      ->orWhere('nip', $identitas)
                      ->orWhere('nik', $identitas);
            })->first();

            if ($user) {
                $userId = $user->id;
                $namaUser = $user->name; // Ambil nama asli dari tabel user
            }
        }

        // Update Data Transaksi
        $transaction->update([
            'user_id'    => $userId,
            'name'       => $namaUser,
            'jenis'      => $request->jenis,
            'nominal'    => $request->nominal,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('transaction.index')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function edit($id)
    {
        $transaksi = Transaction::with('user')->findOrFail($id);

        // Kembalikan data dalam bentuk JSON untuk modal pop-up
        return response()->json([
            'success' => true,
            'data'    => $transaksi
        ]);
    }

    public function cariUser($identitas)
    {
        $user = User::where(function($query) use ($identitas) {
            $query->where('id', $identitas)
                  ->orWhere('email', $identitas)
                  ->orWhere('nis', $identitas)
                  ->orWhere('nip', $identitas)
                  ->orWhere('nik', $identitas);
        })->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'name'    => $user->name
            ]);
        }

        return response()->json([
            'success' => false,
            'name'    => ''
        ]);
    }

    /**
     * Menghapus satu atau beberapa data transaksi secara masal (Bulk Delete).
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids) || count($ids) === 0) {
            return redirect()->route('transaction.index')->withErrors(['Pilih minimal satu data transaksi untuk dihapus.']);
        }

        // Hapus data transaksi berdasarkan array ID yang dicentang
        Transaction::whereIn('id', $ids)->delete();

        return redirect()->route('transaction.index')->with('success', count($ids) . ' data transaksi berhasil dihapus!');
    }
}