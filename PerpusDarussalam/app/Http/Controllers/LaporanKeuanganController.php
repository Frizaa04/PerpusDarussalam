<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tanggal terpilih & Mode Filter (default: 'harian')
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));
        $mode         = $request->get('mode', 'harian'); // 'harian' atau 'mingguan'
        $category     = $request->get('category');
        $search       = $request->get('search');

        $currentCarbon = Carbon::parse($selectedDate);

        // 2. Tentukan Rentang Minggu Penuh (Senin - Minggu)
        $startOfWeek = $currentCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = $currentCarbon->copy()->endOfWeek(Carbon::SUNDAY); // Berakhir di hari Minggu

        $startOfWeekDate = $startOfWeek->format('Y-m-d');
        $endOfWeekDate   = $endOfWeek->format('Y-m-d');

        // Label Bulan & Tahun
        $monthYearLabel = $startOfWeek->translatedFormat('F Y');

        // 3. Loop Array Tanggal (Senin s/d Minggu = 7 Hari)
        $dates = [];
        $tempDate = $startOfWeek->copy();

        while ($tempDate->lte($endOfWeek)) {
            $fullDate = $tempDate->format('Y-m-d');
            $dates[] = [
                'day'       => $tempDate->format('d'),
                'full_date' => $fullDate,
                'is_active' => ($mode === 'harian' && $fullDate === $selectedDate)
            ];
            $tempDate->addDay();
        }

        // 4. Query Dasaran untuk Card Ringkasan berdasarkan Mode
        $applyDateFilter = function ($query) use ($mode, $selectedDate, $startOfWeekDate, $endOfWeekDate) {
            if ($mode === 'mingguan') {
                return $query->whereBetween('tanggal', [$startOfWeekDate, $endOfWeekDate]);
            }
            return $query->whereDate('tanggal', $selectedDate);
        };

        // Hitung Jumlah Transaksi per Kategori
        $pembuatanKartuCount   = $applyDateFilter(Transaction::where('jenis', 'pembuatan_kartu'))->count();
        $kehilanganKartuCount  = $applyDateFilter(Transaction::where('jenis', 'kehilangan_kartu'))->count();
        $keterlambatanBukuCount = $applyDateFilter(Transaction::where('jenis', 'denda_keterlambatan'))->count();

        // 5. Data Detail & Total Uang ketika Card Di-klik
        $dataList = null;
        $totalCategory = 0;

        if ($category) {
            $query = Transaction::with('user')->where('jenis', $category);
            $query = $applyDateFilter($query);

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

            $totalCategory = (clone $query)->sum('nominal');
            $dataList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(10);
        }

        return view('layouts.pages.admin.laporan_keuangan', compact(
            'dates',
            'selectedDate',
            'monthYearLabel',
            'mode',
            'startOfWeekDate',
            'endOfWeekDate',
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