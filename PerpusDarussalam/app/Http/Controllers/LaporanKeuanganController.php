<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Borrowing;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tanggal terpilih (default hari ini)
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));
        $category     = $request->get('category');
        $search       = $request->get('search');

        // 2. Array 7 tanggal horizontal
        $dates = [];
        $baseDate = Carbon::parse($selectedDate);
        for ($i = 6; $i >= 0; $i--) {
            $dt = $baseDate->copy()->subDays($i);
            $dates[] = [
                'day'       => $dt->format('d'),
                'full_date' => $dt->format('Y-m-d'),
                'is_active' => $dt->format('Y-m-d') === $selectedDate
            ];
        }

        // 3. Menghitung JUMLAH ORANG / TRANSAKSI untuk Card Utama
        $pembuatanKartuCount = Transaction::where('jenis', 'pembuatan_kartu')
            ->whereDate('tanggal', $selectedDate)
            ->count();

        $kehilanganKartuCount = Transaction::where('jenis', 'kehilangan_kartu')
            ->whereDate('tanggal', $selectedDate)
            ->count();

        $keterlambatanBukuCount = Borrowing::where('status', 'terlambat')
            ->whereDate('tanggal_kembali', $selectedDate)
            ->count();

        // 4. Data Detail & Total Nominal ketika Card Di-klik
        $dataList = null;
        $totalCategory = 0;

        if ($category === 'pembuatan_kartu' || $category === 'kehilangan_kartu') {
            $query = Transaction::with('user')
                ->where('jenis', $category)
                ->whereDate('tanggal', $selectedDate);

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

            $totalCategory = (clone $query)->sum('nominal');
            $dataList      = $query->orderBy('tanggal', 'desc')->paginate(10);

        } elseif ($category === 'denda_keterlambatan') {
            $query = Borrowing::with(['user', 'bookItem.book'])
                ->where('status', 'terlambat')
                ->whereDate('tanggal_kembali', $selectedDate);

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

            // Hitung total nominal denda untuk kategori ini
            $totalCategory = Borrowing::where('status', 'terlambat')
                ->whereDate('tanggal_kembali', $selectedDate)
                ->get()
                ->sum(function ($b) {
                    $due  = Carbon::parse($b->tanggal_jatuh_tempo);
                    $ret  = $b->tanggal_kembali ? Carbon::parse($b->tanggal_kembali) : Carbon::now();
                    $days = $due->diffInDays($ret, false);
                    $days = $days > 0 ? $days : 1;
                    return $days * 20000;
                });

            $dataList = $query->orderBy('tanggal_kembali', 'desc')->paginate(10);
        }

        return view('layouts.pages.admin.laporan_keuangan', compact(
            'dates',
            'selectedDate',
            'category',
            'search',
            'pembuatanKartuCount',
            'kehilanganKartuCount',
            'keterlambatanBukuCount',
            'totalCategory',
            'dataList'
        ));
    }
}