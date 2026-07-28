<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; 
use App\Models\User;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan query pencarian
        $search = $request->query('search');
        
        // Membangun query dasar dengan eager loading 'user' untuk performa
        $query = Transaction::with('user');

        // Jika ada pencarian, filter berdasarkan nama user atau jenis transaksi
        if ($search) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan nama user yang berelasi
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })
                // Atau cari berdasarkan jenis transaksi (enum)
                ->orWhere('jenis', 'LIKE', "%{$search}%");
            });
        }

        // Urutkan berdasarkan tanggal terbaru dan gunakan paginasi
        $transactions = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Map data enum menjadi teks yang mudah dibaca untuk tampilan
        $transactions->getCollection()->transform(function ($transaction) {
            $labels = [
                'pembuatan_kartu' => 'Pembuatan Kartu',
                'kehilangan_kartu' => 'Kehilangan Kartu',
                'denda_keterlambatan' => 'Denda Keterlambatan',
            ];
            $transaction->jenis_label = $labels[$transaction->jenis] ?? $transaction->jenis;
            return $transaction;
        });

        return view('layouts.pages.admin.transaksi', compact('transactions', 'search'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input sesuai atribut form
        $request->validate([
            'no_identitas' => 'required',
            'nominal'      => 'required|numeric',
            'jenis'        => 'required',
            'tanggal'      => 'required',
            'keterangan'   => 'nullable|string',
        ]);

        // 2. Cari User berdasarkan No Identitas (NIS/NIP/NIK)
        $user = User::where('nis', $request->no_identitas)
                    ->orWhere('nip', $request->no_identitas)
                    ->orWhere('nik', $request->no_identitas)
                    ->first();

        // 3. Normalisasi nilai Enum agar sesuai dengan database ('pembuatan_kartu', dll)
        $jenisMapping = [
            'Pembuatan Kartu'     => 'pembuatan_kartu',
            'Kehilangan Kartu'    => 'kehilangan_kartu',
            'Denda Keterlambatan' => 'denda_keterlambatan',
            'pembuatan_kartu'     => 'pembuatan_kartu',
            'kehilangan_kartu'    => 'kehilangan_kartu',
            'denda_keterlambatan' => 'denda_keterlambatan',
        ];

        $jenisFormatted = $jenisMapping[$request->jenis] ?? $request->jenis;

        // 4. Konversi format tanggal ke YYYY-MM-DD
        try {
            $tanggalFormatted = Carbon::parse($request->tanggal)->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggalFormatted = now()->format('Y-m-d');
        }

        // 5. Simpan Data ke Database
        Transaction::create([
            'user_id'    => $user ? $user->id : null,
            'jenis'      => $jenisFormatted,
            'nominal'    => $request->nominal,
            'keterangan' => $request->keterangan,
            'tanggal'    => $tanggalFormatted,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');
    }

    public function bulkDestroy(Request $request)
    {
        // Validasi input array
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:transactions,id',
        ], [
            'ids.required' => 'Pilih minimal satu data transaksi yang ingin dihapus.'
        ]);

        // Proses hapus masal berdasarkan ID
        Transaction::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Data transaksi yang dipilih berhasil dihapus!');
    }
}