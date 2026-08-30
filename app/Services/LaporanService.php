<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\visits;
use App\Models\Ebook;
use App\Models\Borrowing;
use Carbon\Carbon;

class LaporanService
{
    public function dashboard(Carbon $tanggal, string $mode = 'harian')
    {
        if ($mode === 'mingguan') {
            $start = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $end   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);
            $pengunjungCount = Visits::whereBetween(
                'visited_at',
                [$start, $end]
            )->count();
            $bukuBaruCount = Book::whereBetween(
                'created_at',
                [$start, $end]
            )->count();
            $peminjamanCount = Borrowing::whereBetween(
                'created_at',
                [$start, $end]
            )->count();
            $pengembalianCount = Borrowing::where('status', 'dikembalikan')
                ->whereBetween('updated_at', [$start, $end])
                ->count();
        } elseif ($mode === 'bulanan') {
            $start = $tanggal->copy()->startOfMonth();
            $end   = $tanggal->copy()->endOfMonth();
            $pengunjungCount = Visits::whereBetween(
                'visited_at',
                [$start, $end]
            )->count();
            $bukuBaruCount = Book::whereBetween(
                'created_at',
                [$start, $end]
            )->count();
            $peminjamanCount = Borrowing::whereBetween(
                'created_at',
                [$start, $end]
            )->count();
            $pengembalianCount = Borrowing::where('status', 'dikembalikan')
                ->whereBetween('updated_at', [$start, $end])
                ->count();

        } else {
            $pengunjungCount = Visits::whereDate(
                'visited_at',
                $tanggal
            )->count();
            $bukuBaruCount = Book::whereDate(
                'created_at',
                $tanggal
            )->count();
            $peminjamanCount = Borrowing::whereDate(
                'created_at',
                $tanggal
            )->count();
            $pengembalianCount = Borrowing::where('status', 'dikembalikan')
                ->whereDate('updated_at', $tanggal)
                ->count();
        }
        return [
            'totalKoleksi' => Book::sum('stok'),
            'totalAnggota' => User::count(),
            'pengunjung'   => $pengunjungCount,
            'bukuBaru'     => $bukuBaruCount,
            'peminjaman'   => $peminjamanCount,
            'pengembalian' => $pengembalianCount,
        ];
    }

    public function koleksi(Carbon $tanggal, string $mode = 'harian')
    {
        return [
            'totalKoleksi'        => Book::sum('stok'),
            'totalJudulBukuFisik' => Book::count(),
            'totalEbook'          => Ebook::count(),
            'totalStokBukuFisik'  => Book::sum('stok'),
            'kategoriReferensi'   => Book::whereHas('categories', function($q) {
                $q->where('nama', 'Referensi');
            })->sum('stok'),
            'kategoriBacaan'      => Book::whereHas('categories', function($q) {
                $q->where('nama', 'Bacaan');
            })->sum('stok')
        ];
    }

    public function anggota(Carbon $tanggal, string $mode = 'harian')
    {
        return [
            'totalAnggota' => User::count(),
            'lakiLaki'     => User::where('jenis_kelamin', 'L')->count(),
            'perempuan'    => User::where('jenis_kelamin', 'P')->count(),
            'siswa'        => User::where('status', 'siswa')->count(),
            'guru'         => User::where('status', 'guru')->count(),
        ];
    }

    public function pengunjung(Carbon $tanggal, string $mode = 'harian')
    {
        if ($mode === 'mingguan') {
            $start = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $end   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);
            $baseQuery = Visits::whereBetween(
                'visited_at',
                [$start, $end]
            );

        } elseif ($mode === 'bulanan') {
            $start = $tanggal->copy()->startOfMonth();
            $end   = $tanggal->copy()->endOfMonth();
            $baseQuery = Visits::whereBetween(
                'visited_at',
                [$start, $end]
            );
        } else {
            $baseQuery = Visits::whereDate(
                'visited_at',
                $tanggal
            );
        }

        return [
            'totalPengunjung' => (clone $baseQuery)->count(),
            'lakiLaki' => (clone $baseQuery)
                ->whereHas('user', fn($q) =>
                    $q->where('jenis_kelamin', 'L')
                )->count(),
            'perempuan' => (clone $baseQuery)
                ->whereHas('user', fn($q) =>
                    $q->where('jenis_kelamin', 'P')
                )->count(),
            'siswa' => (clone $baseQuery)
                ->whereHas('user', fn($q) =>
                    $q->where('status', 'siswa')
                )->count(),
            'guru' => (clone $baseQuery)
                ->whereHas('user', fn($q) =>
                    $q->where('status', 'guru')
                )->count(),
        ];
    }

    public function getPeminjamanData(
        Carbon $tanggal,
        string $mode = 'harian'
    ): array {
        if ($mode === 'mingguan') {
            $start = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $end   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);
            $baseQuery = Borrowing::whereBetween(
                'tanggal_pinjam',
                [$start, $end]
            );
        } elseif ($mode === 'bulanan') {
            $start = $tanggal->copy()->startOfMonth();
            $end   = $tanggal->copy()->endOfMonth();
            $baseQuery = Borrowing::whereBetween(
                'tanggal_pinjam',
                [$start, $end]
            );
        } else {
            $baseQuery = Borrowing::whereDate(
                'tanggal_pinjam',
                $tanggal
            );
        }

        $totalPeminjaman = (clone $baseQuery)->count();
        $sedangDipinjam = (clone $baseQuery)
            ->where('status', 'dipinjam')
            ->count();
        $sudahDikembalikan = (clone $baseQuery)
            ->where('status', 'dikembalikan')
            ->count();
        $terlambat = (clone $baseQuery)
            ->where('status', 'terlambat')
            ->count();
        return compact(
            'totalPeminjaman',
            'sedangDipinjam',
            'sudahDikembalikan',
            'terlambat'
        );
    }

    /**
     * Mengambil deretan 7 hari (Senin - Minggu) sesuai minggu dari tanggal aktif
     */
    public function dates(Carbon $selectedDate)
    {
        $dates = [];
        $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dates[] = [
                'day'       => $date->format('d'),
                'full_date' => $date->format('Y-m-d'),
                'is_active' => $date->isSameDay($selectedDate)
            ];
        }
        return $dates;
    }

    public function getChartSirkulasiMingguan(Carbon $tanggal)
    {
        $startOfWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);

        // Ambil data kunjungan dikelompokkan per tanggal dalam minggu 
        $visitsData = Visits::selectRaw('DATE(visited_at) as date, COUNT(*) as count')
            ->whereBetween('visited_at', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->pluck('count', 'date');

        // Ambil data peminjaman dikelompokkan per tanggal dalam minggu 
        $borrowingsData = Borrowing::selectRaw('DATE(tanggal_pinjam) as date, COUNT(*) as count')
            ->whereBetween('tanggal_pinjam', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $pengunjungValues = [];
        $peminjamanValues = [];

        // Loop dari Senin sampai Minggu
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('D, d M'); 
            $pengunjungValues[] = $visitsData[$dateStr] ?? 0;
            $peminjamanValues[] = $borrowingsData[$dateStr] ?? 0;
        }

        return [
            'chartLabels'     => $labels,
            'chartPengunjung' => $pengunjungValues,
            'chartPeminjaman' => $peminjamanValues,
        ];
    }
}