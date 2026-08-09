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

    // Fungsi Import Koleksi Buku Baru
    public function importKoleksi(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new KoleksiImport();
            Excel::import($import, $request->file('file_excel'));

            $imported = $import->importedCount;
            $duplicates = $import->duplicates;

            // Kasus 1: Semua data di Excel duplikat
            if ($imported === 0 && !empty($duplicates)) {
                $duplicateList = implode(', ', array_unique($duplicates));
                return redirect()->back()->with('warning', "Import dibatalkan! Semua data dalam file Excel sudah ada di database: ($duplicateList).");
            }

            // Kasus 2: Sebagian berhasil, sebagian duplikat
            if (!empty($duplicates)) {
                $duplicateList = implode(', ', array_unique($duplicates));
                return redirect()->back()->with('warning', "Berhasil meng-import $imported data buku baru. Namun terdapat " . count($duplicates) . " data yang dilewati karena duplikat: ($duplicateList).");
            }

            // Kasus 3: Semua data baru berhasil di-import
            return redirect()->back()->with('success', "Berhasil meng-import $imported data koleksi buku baru!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import data: ' . $e->getMessage());
        }
    }

    public function exportAnggotaExcel()
    {
        return Excel::download(new AnggotaExport, 'Laporan_Anggota_' . date('Y-m-d') . '.xlsx');
    }

    // Fungsi Import Anggota Baru (Sudah Diperbarui)
    public function importAnggota(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new AnggotaImport();
            Excel::import($import, $request->file('file_excel'));

            $imported = $import->importedCount;
            $duplicates = $import->duplicates;

            // Kasus 1: Semua data di Excel duplikat / sudah ada
            if ($imported === 0 && !empty($duplicates)) {
                $duplicateList = implode(', ', array_unique($duplicates));
                return redirect()->back()->with('warning', "Import dibatalkan! Semua data anggota dalam file Excel sudah ada di database: ($duplicateList).");
            }

            // Kasus 2: Sebagian berhasil, sebagian duplikat
            if (!empty($duplicates)) {
                $duplicateList = implode(', ', array_unique($duplicates));
                return redirect()->back()->with('warning', "Berhasil meng-import $imported data anggota baru. Namun terdapat " . count($duplicates) . " data yang dilewati karena duplikat: ($duplicateList).");
            }

            // Kasus 3: Semua data berhasil di-import tanpa ada duplikat
            return redirect()->back()->with('success', "Berhasil meng-import $imported data anggota baru!");

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
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Absensi_' . $tanggal . '.xlsx';

        return Excel::download(new AttendanceExport($tanggal), $namaFile);
    }
}