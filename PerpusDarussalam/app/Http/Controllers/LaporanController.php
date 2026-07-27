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
use Maatwebsite\Excel\Facades\Excel;


class LaporanController extends Controller
{

    public function __construct(
        protected LaporanService $laporanService
    ){}

    public function index(Request $request)
    {
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : today();
        $data = $this->laporanService
            ->dashboard($selectedDate);
        $data['dates'] = $this->laporanService
            ->dates($selectedDate);
        $data['selectedDate'] = $selectedDate;
        return view(
            'layouts.pages.admin.laporan',
            $data
        );
    }

    public function koleksi(Request $request)
    {
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : today();
        $data = $this->laporanService->koleksi();
        $data['dates'] = $this->laporanService
            ->dates($selectedDate);
        $data['selectedDate'] = $selectedDate;
        return view(
            'layouts.pages.admin.laporan_koleksi',
            $data
        );
    }

    public function anggota(Request $request)
    {
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : today();
        $data = $this->laporanService->anggota();
        $data['dates'] = $this->laporanService
            ->dates($selectedDate);
        $data['selectedDate'] = $selectedDate;
        return view(
            'layouts.pages.admin.laporan_anggota',
            $data
        );
    }

    public function pengunjung(Request $request)
    {
        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : today();
        $data = $this->laporanService
            ->pengunjung($selectedDate);
        $data['dates'] = $this->laporanService
            ->dates($selectedDate);
        $data['selectedDate'] = $selectedDate;
        return view(
            'layouts.pages.admin.laporan_pengunjung',
            $data
        );
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Koleksi_' . $tanggal . '.xlsx';

        return Excel::download(new KoleksiExport($tanggal), $namaFile);
    }

    public function exportAnggota()
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

    public function exportPengunjungExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Pengunjung_' . $tanggal . '.xlsx';

        // Jika nanti sudah membuat class PengunjungExport:
        // return Excel::download(new \App\Exports\PengunjungExport($tanggal), $namaFile);

        return back()->with('info', 'Fitur export pengunjung sedang disiapkan.');
    }

    public function exportPeminjamanExcel(Request $request)
    {
        $tanggal = $request->query('date', today()->format('Y-m-d'));
        $namaFile = 'Laporan_Peminjaman_' . $tanggal . '.xlsx';

        return Excel::download(new BorrowingExport($tanggal), $namaFile);
    }
}