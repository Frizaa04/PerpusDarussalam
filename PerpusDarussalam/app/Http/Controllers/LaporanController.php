<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\visits;
use App\Models\Borrowing;
use Carbon\Carbon;
use App\Exports\KoleksiExport;
use App\Exports\AnggotaExport;
use App\Exports\BorrowingExport;
use App\Imports\AnggotaImport;
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
        $selectedDate = $request->date ? Carbon::parse($request->date) : today();
        $mode = $request->get('mode', 'harian');

        return [
            'selectedDate'    => $selectedDate,
            'mode'            => $mode,
            'dates'           => $this->laporanService->dates($selectedDate),
            'monthYearLabel'  => $selectedDate->translatedFormat('F Y'),
            'startOfWeekDate' => $selectedDate->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
            'endOfWeekDate'   => $selectedDate->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'),
        ];
    }

    public function index(Request $request)
    {
        $params = $this->getCommonParams($request);
    
        // Ambil data dashboard & data chart mingguan
        $dataDashboard = $this->laporanService->dashboard($params['selectedDate'], $params['mode']);
        $dataChart     = $this->laporanService->getChartSirkulasiMingguan($params['selectedDate']);
        $data = array_merge($dataDashboard, $dataChart);
        return view('layouts.pages.admin.laporan', array_merge($data, $params));
    }

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

    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        return Excel::download(new KoleksiExport($tanggal), 'Laporan_Koleksi_' . $tanggal . '.xlsx');
    }

// Sesudah (Perbaikan)
    public function exportAnggotaExcel()
    {
        return Excel::download(new AnggotaExport, 'Laporan_Anggota_' . date('Y-m-d') . '.xlsx');
    }
    public function importAnggota(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new AnggotaImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data anggota berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import data: ' . $e->getMessage());
        }
    }

    public function exportPeminjamanExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        return Excel::download(new BorrowingExport($tanggal), 'Laporan_Peminjaman_' . $tanggal . '.xlsx');
    }

    public function exportAttendanceExcel(Request $request)
    {
        // Mengambil parameter tanggal dari URL, default ke hari ini jika kosong
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        
        // Nama file hasil unduhan
        $namaFile = 'Laporan_Absensi_' . $tanggal . '.xlsx';

        return Excel::download(new AttendanceExport($tanggal), $namaFile);
    }
}