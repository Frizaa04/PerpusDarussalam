<?php

namespace App\Exports;

use App\Models\Visit;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $selectedDate;
    protected $jumlahData = 0;

    public function __construct($date)
    {
        $this->selectedDate = $date ? Carbon::parse($date) : today();
    }
    
    public function array(): array
    {
        $data = [];

        // Header Tabel Laporan Kunjungan / Absensi
        $data[] = ['No', 'Waktu Kunjungan', 'No Identitas / ID', 'Nama Pengunjung', 'Kategori / Role'];

        // Ambil data kunjungan berdasarkan tanggal yang dipilih pada kolom 'visited_at'
        $visits = Visit::with('user')
            ->whereDate('visited_at', $this->selectedDate->format('Y-m-d'))
            ->get();
            
        $this->jumlahData = $visits->count();
        $no = 1;

        foreach ($visits as $visit) {
            $data[] = [
                $no++,
                $visit->visited_at ? Carbon::parse($visit->visited_at)->format('Y-m-d H:i:s') : '-',
                $visit->user->nis ?? $visit->user->nip ?? $visit->user->id ?? '-',
                $visit->user->name ?? '-',
                $visit->user->role ?? $visit->user->kategori ?? '-', // Sesuaikan kolom role/kategori di tabel user Anda jika ada
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleHeaderHijau = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '004D40'], // Tema warna hijau tua aplikasi
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Terapkan warna ke Header (Baris 1 dari kolom A sampai E)
        $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderHijau);

        // Beri border tipis untuk seluruh baris isi data
        if ($this->jumlahData > 0) {
            $sheet->getStyle('A2:E' . (1 + $this->jumlahData))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [];
    }
}