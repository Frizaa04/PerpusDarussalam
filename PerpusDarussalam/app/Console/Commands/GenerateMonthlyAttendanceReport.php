<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Visits;

class GenerateMonthlyAttendanceReport extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'laporan:pengunjung-bulanan';

    /**
     * Deskripsi command.
     */
    protected $description = 'Membuat laporan pengunjung bulanan';

    /**
     * Jalankan command.
     */
    public function handle()
    {
        // Ambil bulan sebelumnya
        $bulanLalu = Carbon::now()->subMonth();

        $startDate = $bulanLalu->copy()->startOfMonth();
        $endDate   = $bulanLalu->copy()->endOfMonth();

        // Nama bulan dan tahun
        $namaBulan = $bulanLalu->translatedFormat('F');
        $tahun     = $bulanLalu->format('Y');

        // Nama file
        $namaFile = 'Laporan_Absensi_' . $namaBulan . '_' . $tahun . '.xlsx';

        // Folder penyimpanan
        $folder = 'reports/pengunjung/' . $tahun;

        // Pastikan folder tersedia
        Storage::disk('local')->makeDirectory($folder);

        // Path lengkap file
        $path = $folder . '/' . $namaFile;

        $this->info('==========================================');
        $this->info('GENERATE LAPORAN PENGUNJUNG BULANAN');
        $this->info('==========================================');

        $this->info(
            'Periode: ' .
            $startDate->format('d-m-Y') .
            ' s/d ' .
            $endDate->format('d-m-Y')
        );

        $this->info('Membuat laporan...');

        // Generate dan simpan Excel
        Excel::store(
            new AttendanceExport($startDate, $endDate),
            $path,
            'local'
        );

        if (!Storage::disk('local')->exists($path)) {
            $this->error('Laporan gagal dibuat. Data lama TIDAK akan dihapus.');

            return Command::FAILURE;
        }

        $this->info('✓ Laporan berhasil dibuat.');
        $this->info('File: ' . $path);
        $this->deleteOldVisits();

        return Command::SUCCESS;
        
    }

private function deleteOldVisits()
{
    $batasTanggal = Carbon::now()
        ->subYear()
        ->startOfDay();

    $jumlahData = Visits::where(
        'visited_at',
        '<',
        $batasTanggal
    )->count();

    if ($jumlahData === 0) {
        $this->info('Tidak ada data kunjungan yang lebih dari 1 tahun.');
        return;
    }

    Visits::where(
        'visited_at',
        '<',
        $batasTanggal
    )->delete();

    $this->info(
        "✓ {$jumlahData} data kunjungan lama berhasil dihapus."
    );
}
}