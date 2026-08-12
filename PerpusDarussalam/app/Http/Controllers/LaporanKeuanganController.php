<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tanggal terpilih & Mode Filter (default: objek Carbon hari ini)
        $selectedDateInput = $request->get('date');
        $selectedDate      = $selectedDateInput ? Carbon::parse($selectedDateInput) : Carbon::now();
        $mode              = $request->get('mode', 'harian'); // 'harian' atau 'mingguan'
        $category          = $request->get('category');
        $search            = $request->get('search');

        $currentCarbon = $selectedDate->copy();

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
                'is_active' => ($mode === 'harian' && $fullDate === $selectedDate->format('Y-m-d'))
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
        $pembuatanKartuCount    = $applyDateFilter(Transaction::where('jenis', 'pembuatan_kartu'))->count();
        $kehilanganKartuCount   = $applyDateFilter(Transaction::where('jenis', 'kehilangan_kartu'))->count();
        $keterlambatanBukuCount = $applyDateFilter(Transaction::where('jenis', 'denda_keterlambatan'))->count();
        $kehilanganBukuCount    = $applyDateFilter(Transaction::where('jenis', 'kehilangan_buku'))->count();
        
        // Hitung Total Seluruh Nominal dari Ketiga Kategori Berdasarkan Filter Tanggal/Mode
        $totalSemua = $applyDateFilter(Transaction::whereIn('jenis', [
            'pembuatan_kartu', 
            'kehilangan_kartu', 
            'denda_keterlambatan',
            'kehilangan_buku'
        ]))->sum('nominal');

        // 5. Data Detail & Total Uang 
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
            'kehilanganBukuCount',
            'totalSemua',     
            'totalCategory',
            'dataList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tanggalInput = $request->query('date', today()->format('Y-m-d'));
        $mode = $request->query('mode', 'harian');

        $carbonDate = Carbon::parse($tanggalInput);

        if ($mode === 'mingguan') {
            // Jika mode mingguan, ambil dari Senin sampai Minggu 
            $startDate = $carbonDate->copy()->startOfWeek()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfWeek()->format('Y-m-d');
        } elseif ($mode === 'bulanan') {
            // Jika mode bulanan, ambil dari tanggal 1 sampai akhir bulan
            $startDate = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfMonth()->format('Y-m-d');
        } else {
            // Mode harian
            $startDate = $tanggalInput;
            $endDate = $tanggalInput;
        }

        return Excel::download(new TransactionExport($startDate, $endDate), 'laporan-transaksi.xlsx');
    }
}