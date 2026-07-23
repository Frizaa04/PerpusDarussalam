<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\visits;
use App\Models\Borrowing;
use Carbon\Carbon;
use App\Exports\KoleksiExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // Halaman Laporan Ringkasan Utama (Foto 1)
    public function index(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        $totalKoleksi = Book::sum('stok') ?? 0;
        $totalAnggota = User::count() ?? 0;
        $pengunjung   = visits::whereDate('created_at', $selectedDate)->count();
        $bukuBaru     = Book::whereDate('created_at', $selectedDate)->count();
        $peminjaman   = Borrowing::whereDate('created_at', $selectedDate)->count();
        $pengembalian = Borrowing::where('status', 'dikembalikan')
            ->whereDate('updated_at', $selectedDate)
            ->count();

        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dates[] = [
                'day'       => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_active' => $date->isSameDay($selectedDate),
            ];
        }

        return view('layouts.pages.admin.laporan', compact(
            'totalKoleksi', 'totalAnggota', 'pengunjung', 
            'bukuBaru', 'peminjaman', 'pengembalian', 
            'dates', 'selectedDate'
        ));
    }

    // Halaman Laporan Detail Koleksi (Foto 2)
    public function koleksi(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        // Query Statistik Koleksi
        $totalKoleksi           = Book::sum('stok') ?? 0;
        $totalJudulBukuFisik    = Book::count() ?? 0;
        $totalEbook             = 0; // Sesuaikan jika ada model Ebook terpisah
        $totalStokBukuFisik     = Book::sum('stok') ?? 0;

        // Filter stok buku berdasarkan nama kategori via relasi 'categories'
        $kategoriReferensi = Book::whereHas('categories', function ($query) {
            $query->where('nama', 'Referensi'); 
            // Catatan: Jika nama kolom nama kategori di tabel categories kamu adalah 'nama' atau 'kategori', 
            // sesuaikan 'nama_kategori' di atas (misal: 'nama', 'Referensi').
        })->sum('stok') ?? 0;

        $kategoriBacaan = Book::whereHas('categories', function ($query) {
            $query->where('nama', 'Bacaan');
        })->sum('stok') ?? 0;

        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dates[] = [
                'day'       => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_active' => $date->isSameDay($selectedDate),
            ];
        }

        return view('layouts.pages.admin.laporan_koleksi', compact(
            'totalKoleksi', 'totalJudulBukuFisik', 'totalEbook',
            'totalStokBukuFisik', 'kategoriReferensi', 'kategoriBacaan',
            'dates', 'selectedDate'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Koleksi_' . $tanggal . '.xlsx';

        return Excel::download(new KoleksiExport($tanggal), $namaFile);
    }
}