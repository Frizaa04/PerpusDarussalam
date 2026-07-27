<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // Pastikan Anda sudah membuat Model Transaction
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
                // Cari dberdasarkan nama user yang berelasi
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })
                // Atau cari berdasarkan jenis transaksi (enum)
                ->orWhere('jenis', 'LIKE', "%{$search}%");
            });
        }

        // Urutkan berdasarkan tanggal terbaru dan gunakan paginasi (misal 10 data per halaman)
        // links() di blade akan otomatis generate tombol 1 2 3 >
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
}