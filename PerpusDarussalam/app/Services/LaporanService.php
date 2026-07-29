<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Visits;
use App\Models\Borrowing;
use Carbon\Carbon;

class LaporanService
{
    public function dashboard(Carbon $tanggal, string $mode = 'harian')
    {
        if ($mode === 'mingguan') {
            $startOfWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);

            $pengunjungCount   = Visits::whereBetween('visited_at', [$startOfWeek, $endOfWeek])->count();
            $bukuBaruCount     = Book::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $peminjamanCount   = Borrowing::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $pengembalianCount = Borrowing::where('status', 'dikembalikan')
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->count();
        } else {
            $pengunjungCount   = Visits::whereDate('visited_at', $tanggal)->count();
            $bukuBaruCount     = Book::whereDate('created_at', $tanggal)->count();
            $peminjamanCount   = Borrowing::whereDate('created_at', $tanggal)->count();
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
            'totalEbook'          => 0,
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
            'siswa'        => User::where('role', 'siswa')->count(),
            'guru'         => User::where('role', 'guru')->count(),
            'umum'         => User::where('role', 'umum')->count()
        ];
    }

    public function pengunjung(Carbon $tanggal, string $mode = 'harian')
    {
        if ($mode === 'mingguan') {
            $start = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $end   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);

            $baseQuery = Visits::whereBetween('visited_at', [$start, $end]);
        } else {
            $baseQuery = Visits::whereDate('visited_at', $tanggal);
        }

        return [
            'totalPengunjung' => (clone $baseQuery)->count(),
            'lakiLaki'        => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis_kelamin', 'L'))->count(),
            'perempuan'       => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('jenis_kelamin', 'P'))->count(),
            'siswa'           => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('role', 'siswa'))->count(),
            'guru'            => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('role', 'guru'))->count(),
            'umum'            => (clone $baseQuery)->whereHas('user', fn($q) => $q->where('role', 'umum'))->count(),
        ];
    }

    public function getPeminjamanData(Carbon $tanggal, string $mode = 'harian'): array
    {
        if ($mode === 'mingguan') {
            $start = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
            $end   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);

            $totalPeminjaman   = Borrowing::whereBetween('tanggal_pinjam', [$start, $end])->count();
            $sedangDipinjam    = Borrowing::whereBetween('tanggal_pinjam', [$start, $end])->where('status', 'dipinjam')->count();
            $sudahDikembalikan = Borrowing::whereBetween('tanggal_pinjam', [$start, $end])->where('status', 'dikembalikan')->count();
            $terlambat         = Borrowing::whereBetween('tanggal_pinjam', [$start, $end])->where('status', 'terlambat')->count();
        } else {
            $totalPeminjaman   = Borrowing::whereDate('tanggal_pinjam', $tanggal)->count();
            $sedangDipinjam    = Borrowing::whereDate('tanggal_pinjam', $tanggal)->where('status', 'dipinjam')->count();
            $sudahDikembalikan = Borrowing::whereDate('tanggal_pinjam', $tanggal)->where('status', 'dikembalikan')->count();
            $terlambat         = Borrowing::whereDate('tanggal_pinjam', $tanggal)->where('status', 'terlambat')->count();
        }

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
}