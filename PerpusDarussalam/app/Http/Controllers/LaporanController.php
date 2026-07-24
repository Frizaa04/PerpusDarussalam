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
    /**
     * Halaman Utama Ringkasan Laporan
     */
    public function index(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        $totalKoleksi = Book::sum('stok') ?? 0;
        $totalAnggota = User::count() ?? 0;
        $pengunjung   = visits::whereDate('visited_at', $selectedDate)->count();
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

    /**
     * Halaman Laporan Detail Koleksi
     */
    public function koleksi(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        // Query Statistik Koleksi
        $totalKoleksi        = Book::sum('stok') ?? 0;
        $totalJudulBukuFisik = Book::count() ?? 0;
        $totalEbook          = 0; // Sesuaikan jika ada model Ebook terpisah
        $totalStokBukuFisik  = Book::sum('stok') ?? 0;

        // Filter stok buku berdasarkan nama kategori via relasi 'categories'
        $kategoriReferensi = Book::whereHas('categories', function ($query) {
            $query->where('nama', 'Referensi');
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

    /**
     * Halaman Laporan Detail Anggota
     */
    public function anggota(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        // Query Statistik Anggota
        $totalAnggota = User::count() ?? 0;
        
        // Sesuaikan kolom 'jenis_kelamin' dan 'role' sesuai struktur tabel users kamu
        $lakiLaki  = User::where('jenis_kelamin', 'L')->count() ?? 0;
        $perempuan = User::where('jenis_kelamin', 'P')->count() ?? 0;
        
        $siswa = User::where('role', 'siswa')->count() ?? 0;
        $guru  = User::where('role', 'guru')->count() ?? 0;
        $umum  = User::where('role', 'umum')->count() ?? 0;

        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dates[] = [
                'day'       => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_active' => $date->isSameDay($selectedDate),
            ];
        }

        return view('layouts.pages.admin.laporan_anggota', compact(
            'totalAnggota', 'lakiLaki', 'perempuan',
            'siswa', 'guru', 'umum',
            'dates', 'selectedDate'
        ));
    }

    /**
     * Halaman Laporan Detail Pengunjung
     */
    public function pengunjung(Request $request)
    {
        $selectedDate = $request->query('date') ? Carbon::parse($request->query('date')) : today();

        // Query visits berdasarkan tanggal visited_at
        $visitsQuery = visits::whereDate('visited_at', $selectedDate);

        // Total Pengunjung
        $totalPengunjung = (clone $visitsQuery)->count();

        // Hitung statistik berdasarkan data user terhubung
        $lakiLaki = (clone $visitsQuery)->whereHas('user', function ($q) {
            $q->where('jenis_kelamin', 'L');
        })->count();

        $perempuan = (clone $visitsQuery)->whereHas('user', function ($q) {
            $q->where('jenis_kelamin', 'P');
        })->count();

        $siswa = (clone $visitsQuery)->whereHas('user', function ($q) {
            $q->where('role', 'siswa');
        })->count();

        $guru = (clone $visitsQuery)->whereHas('user', function ($q) {
            $q->where('role', 'guru');
        })->count();

        $umum = (clone $visitsQuery)->whereHas('user', function ($q) {
            $q->where('role', 'umum');
        })->count();

        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dates[] = [
                'day'       => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_active' => $date->isSameDay($selectedDate),
            ];
        }

        return view('layouts.pages.admin.laporan_pengunjung', compact(
            'totalPengunjung', 'lakiLaki', 'perempuan',
            'siswa', 'guru', 'umum',
            'dates', 'selectedDate'
        ));
    }

    /**
     * Export Laporan Koleksi ke Excel
     */
    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Koleksi_' . $tanggal . '.xlsx';

        return Excel::download(new KoleksiExport($tanggal), $namaFile);
    }

    /**
     * Export Laporan Pengunjung ke Excel
     */
    public function exportPengunjungExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Pengunjung_' . $tanggal . '.xlsx';

        // Jika nanti sudah membuat class PengunjungExport:
        // return Excel::download(new \App\Exports\PengunjungExport($tanggal), $namaFile);

        return back()->with('info', 'Fitur export pengunjung sedang disiapkan.');
    }
}