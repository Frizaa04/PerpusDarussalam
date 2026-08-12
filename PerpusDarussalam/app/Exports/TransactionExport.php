<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransactionExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $jumlahData = 0;

    public function __construct($startDate, $endDate = null)
    {
        // Jika hanya dikirim satu tanggal
        $this->startDate = $startDate ? Carbon::parse($startDate) : today();
        $this->endDate = $endDate ? Carbon::parse($endDate) : $this->startDate;
    }
    
    public function array(): array
    {
        $data = [];
        $data[] = ['No', 'Nama', 'Jenis Transaksi', 'Nominal', 'Tanggal', 'Keterangan'];

        // Gunakan whereBetween untuk mengambil data dalam rentang tanggal
        $transactions = Transaction::with('user')
            ->whereBetween('tanggal', [
                $this->startDate->format('Y-m-d 00:00:00'), 
                $this->endDate->format('Y-m-d 23:59:59')
            ])
            ->get();
            
        $this->jumlahData = $transactions->count();
        $no = 1;
        $totalNominal = 0; // Variabel untuk menampung jumlah total

        $jenisLabels = [
            'pembuatan_kartu'     => 'Pembuatan Kartu',
            'kehilangan_kartu'    => 'Kehilangan Kartu',
            'denda_keterlambatan' => 'Denda Keterlambatan Buku',
            'kehilangan_buku'     => 'Kehilangan Buku',
        ];

        foreach ($transactions as $trx) {
            $nominal = (float) ($trx->nominal ?? 0);
            $totalNominal += $nominal;

            $data[] = [
                $no++,
                $trx->user->name ?? $trx->nama ?? '-',
                $jenisLabels[$trx->jenis] ?? ($trx->jenis ?? '-'),
                $nominal,
                $trx->tanggal ?? '-',
                $trx->keterangan ?? '-',
            ];
        }

        // Jika ada data, tambahkan baris kosong lalu baris Total di bawahnya
        if ($this->jumlahData > 0) {
            // Baris kosong
            $data[] = ['', '', '', '', '', ''];
            
            // Baris Total langsung menampilkan angka hasil hitung 
            $data[] = ['', '', 'TOTAL KESELURUHAN', $totalNominal, '', ''];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $styleHeaderHijau = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '004D40'], 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        // Terapkan warna ke Header 
        $sheet->getStyle('A1:F1')->applyFromArray($styleHeaderHijau);

        // Border tipis untuk seluruh isi data
        if ($this->jumlahData > 0) {
            $lastRowData = 1 + $this->jumlahData;
            $totalRowIndex = $lastRowData + 2;

            // Border untuk data
            $sheet->getStyle('A2:F' . $lastRowData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Styling baris Total 
            $sheet->getStyle("A{$totalRowIndex}:F{$totalRowIndex}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                ],
            ]);
        }

        return [];
    }
}