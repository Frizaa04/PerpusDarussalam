<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
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
        // Keterlambatan buku juga mengambil count dari tabel transactions
        $pembuatanKartuCount = Transaction::where('jenis', 'pembuatan_kartu')
            ->whereDate('tanggal', $selectedDate)
            ->count();

        $kehilanganKartuCount = Transaction::where('jenis', 'kehilangan_kartu')
            ->whereDate('tanggal', $selectedDate)
            ->count();

        $keterlambatanBukuCount = Transaction::where('jenis', 'denda_keterlambatan')
            ->whereDate('tanggal', $selectedDate)
            ->count();

        // 4. Data Detail & Total Nominal ketika Card Di-klik
        $dataList = null;
        $totalCategory = 0;

        if ($category) {
            $query = Transaction::with('user')
                ->where('jenis', $category)
                ->whereDate('tanggal', $selectedDate);

            // Filter pencarian nama user
            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

            // Hitung total nominal untuk kategori terpilih
            $totalCategory = (clone $query)->sum('nominal');

            // Ambil data transaksi beserta paginasi
            $dataList = $query->orderBy('tanggal', 'desc')->paginate(10);
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