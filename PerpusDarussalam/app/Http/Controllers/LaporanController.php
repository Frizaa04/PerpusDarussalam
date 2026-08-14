<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\visits;
use App\Models\Borrowing;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Exports\KoleksiExport;
use App\Exports\AnggotaExport;
use App\Exports\BorrowingExport;
use App\Exports\TransactionExport;
use App\Imports\AnggotaImport;
use App\Imports\KoleksiImport;
use App\Services\LaporanService;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function __construct(
        protected LaporanService $laporanService
    ) {}

    private function getCommonParams(Request $request)
    {
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : today();

        $mode = $request->get('mode', 'harian');

        // Default
        $startDate = $selectedDate->copy()->startOfDay();
        $endDate   = $selectedDate->copy()->endOfDay();

        // Jika mingguan
        if ($mode === 'mingguan') {
            $startDate = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
            $endDate   = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY);
        }

        // Jika bulanan
        if ($mode === 'bulanan') {
            $startDate = $selectedDate->copy()->startOfMonth();
            $endDate   = $selectedDate->copy()->endOfMonth();
        }

        // Navigasi tanggal hanya digunakan untuk mode harian
        $dates = [];

        if ($mode === 'harian') {

            $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek   = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY);

            $tempDate = $startOfWeek->copy();

            while ($tempDate->lte($endOfWeek)) {

                $fullDate = $tempDate->format('Y-m-d');

                $dates[] = [
                    'day'       => $tempDate->format('d'),
                    'full_date' => $fullDate,
                    'is_active' => $fullDate === $selectedDate->format('Y-m-d')
                ];

                $tempDate->addDay();
            }
        }

        return [
            'selectedDate'    => $selectedDate,
            'mode'            => $mode,
            'dates'           => $dates,

            'monthYearLabel'  => $selectedDate->translatedFormat('F Y'),

            'startOfWeekDate' => $startDate->format('Y-m-d'),
            'endOfWeekDate'   => $endDate->format('Y-m-d'),

            // Tambahan yang lebih umum
            'startDate'       => $startDate->format('Y-m-d'),
            'endDate'         => $endDate->format('Y-m-d'),
        ];
    }

    public function index(Request $request)
    {
        $params = $this->getCommonParams($request);
        $category = $request->get('category');
        $search = $request->get('search');
    
        // 1. Data Laporan Madrasah
        $dataDashboard = $this->laporanService->dashboard($params['selectedDate'], $params['mode']);
        $dataChart     = $this->laporanService->getChartSirkulasiMingguan($params['selectedDate']);
        
        // 2. Logika Query Keuangan
        $applyDateFilter = function ($query) use ($params) {

            if (in_array($params['mode'], ['mingguan', 'bulanan'])) {

                return $query->whereBetween('tanggal', [
                    $params['startDate'],
                    $params['endDate']
                ]);
            }

            return $query->whereDate(
                'tanggal',
                $params['selectedDate']
            );
        };

        // Hitung statistik keuangan
        $pembuatanKartuCount    = $applyDateFilter(Transaction::where('jenis', 'pembuatan_kartu'))->count();
        $kehilanganKartuCount   = $applyDateFilter(Transaction::where('jenis', 'kehilangan_kartu'))->count();
        $keterlambatanBukuCount = $applyDateFilter(Transaction::where('jenis', 'denda_keterlambatan'))->count();
        $kehilanganBukuCount    = $applyDateFilter(Transaction::where('jenis', 'kehilangan_buku'))->count();
        
        $totalSemua = $applyDateFilter(Transaction::whereIn('jenis', [
            'pembuatan_kartu', 'kehilangan_kartu', 'denda_keterlambatan', 'kehilangan_buku'
        ]))->sum('nominal');

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
            $dataList = $query
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        // Gabungkan semua data
        $data = array_merge($dataDashboard, $dataChart, [
            'category'               => $category,
            'search'                 => $search,
            'pembuatanKartuCount'    => $pembuatanKartuCount,
            'kehilanganKartuCount'   => $kehilanganKartuCount,
            'keterlambatanBukuCount' => $keterlambatanBukuCount,
            'kehilanganBukuCount'    => $kehilanganBukuCount,
            'totalSemua'             => $totalSemua,
            'totalCategory'          => $totalCategory,
            'dataList'               => $dataList,
        ]);

        return view('layouts.pages.admin.laporan', array_merge($data, $params));
    }

    // --- Fungsi Laporan Lainnya ---

    public function koleksi(Request $request)
    {
        $params = $this->getCommonParams($request);
        $data = $this->laporanService->koleksi($params['selectedDate'], $params['mode']);
        return view('layouts.pages.admin.laporan_koleksi', array_merge($data, $params));
    }

    public function anggota(Request $request)
    {
        $params = $this->getCommonParams($request);
        $data = $this->laporanService->anggota($params['selectedDate'], $params['mode']);
        return view('layouts.pages.admin.laporan_anggota', array_merge($data, $params));
    }

    public function pengunjung(Request $request)
    {
        $params = $this->getCommonParams($request);
        $data = $this->laporanService->pengunjung($params['selectedDate'], $params['mode']);
        return view('layouts.pages.admin.laporan_absensi', array_merge($data, $params));
    }

    public function peminjaman(Request $request)
    {
        $params = $this->getCommonParams($request);
        $data = $this->laporanService->getPeminjamanData($params['selectedDate'], $params['mode']);
        return view('layouts.pages.admin.laporan_peminjaman', array_merge($data, $params));
    }

    // --- Fungsi Export & Import ---

    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        return Excel::download(new KoleksiExport($tanggal), 'Laporan_Koleksi_' . $tanggal . '.xlsx');
    }

    public function importKoleksi(Request $request)
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:5120']);
        try {
            $import = app(KoleksiImport::class);  
            Excel::import($import, $request->file('file_excel'));
            return redirect()->back()->with('success', "Berhasil meng-import " . $import->importedCount . " data koleksi buku baru!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportAnggotaExcel(Request $request)
    {
        $params = $this->getCommonParams($request);
        $startDate = ($params['mode'] === 'mingguan') ? $params['startOfWeekDate'] : $params['selectedDate']->format('Y-m-d');
        $endDate = ($params['mode'] === 'mingguan') ? $params['endOfWeekDate'] : $startDate;
        return Excel::download(new AnggotaExport($startDate, $endDate), 'Laporan_Anggota_' . $startDate . '.xlsx');
    }

    public function importAnggota(Request $request)
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:2048']);
        try {
            $import = new AnggotaImport();
            Excel::import($import, $request->file('file_excel'));
            return redirect()->back()->with('success', "Berhasil meng-import " . $import->importedCount . " data anggota baru!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportPeminjamanExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        return Excel::download(new BorrowingExport($tanggal), 'Laporan_Peminjaman_' . $tanggal . '.xlsx');
    }

    public function exportAttendanceExcel(Request $request)
    {
        $mode = $request->get('mode', 'harian');
        $date = $request->date ? Carbon::parse($request->date) : today();
        $startDate = ($mode === 'mingguan') ? $date->copy()->startOfWeek() : $date;
        $endDate = ($mode === 'mingguan') ? $date->copy()->endOfWeek() : $date;
        return Excel::download(new AttendanceExport($startDate, $endDate), 'Laporan_Absensi.xlsx');
    }

    public function exportKeuanganExcel(Request $request)
    {
        $tanggalInput = $request->query('date', today()->format('Y-m-d'));
        $mode = $request->query('mode', 'harian');
        $carbonDate = Carbon::parse($tanggalInput);

        $startDate = ($mode === 'mingguan') ? $carbonDate->copy()->startOfWeek()->format('Y-m-d') : $tanggalInput;
        $endDate = ($mode === 'mingguan') ? $carbonDate->copy()->endOfWeek()->format('Y-m-d') : $tanggalInput;

        return Excel::download(new TransactionExport($startDate, $endDate), 'laporan-transaksi.xlsx');
    }
}