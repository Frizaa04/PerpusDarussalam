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
        // Pastikan startDate berada di 00:00:00
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : today()->startOfDay();

        // Jika endDate kosong, gunakan copy dari startDate agar objek Carbon terpisah
        // Set endDate ke akhir hari (23:59:59)
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : $this->startDate->copy()->endOfDay();
    }
    
    public function array(): array
    {
        $data = [];
        $data[] = ['No', 'Nama', 'Jenis Transaksi', 'Nominal', 'Tanggal', 'Keterangan'];

        // Menggunakan rentang tanggal yang sudah diparse presisi
        $transactions = Transaction::with('user')
            ->whereBetween('tanggal', [
                $this->startDate->format('Y-m-d H:i:s'), 
                $this->endDate->format('Y-m-d H:i:s')
            ])
            ->get();
            
        $this->jumlahData = $transactions->count();
        $no = 1;
        $totalNominal = 0;

        $jenisLabels = [
            'pembuatan_kartu'     => 'Pembuatan Kartu',
            'kehilangan_kartu'    => 'Kehilangan Kartu',
            'denda_keterlambatan' => 'Denda Keterlambatan Buku',
            'kehilangan_buku'     => 'Kehilangan Buku',
            'perpanjang_kartu'    => 'Perpanjang Kartu',
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

        if ($this->jumlahData > 0) {
            $data[] = ['', '', '', '', '', ''];
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

        $sheet->getStyle('A1:F1')->applyFromArray($styleHeaderHijau);

        if ($this->jumlahData > 0) {
            $lastRowData = 1 + $this->jumlahData;
            $totalRowIndex = $lastRowData + 2;

            $sheet->getStyle('A2:F' . $lastRowData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

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