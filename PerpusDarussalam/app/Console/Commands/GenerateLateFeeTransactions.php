<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Transaction;
use App\Models\Tarif;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateLateFeeTransactions extends Command
{
    protected $signature = 'borrowings:generate-late-fees';
    protected $description = 'Membuat/memperbarui transaksi denda keterlambatan untuk peminjaman yang telat';

    public function handle(): void
    {
        $tarif = Tarif::where('jenis', 'denda_keterlambatan')->first();
        $tarifPerHari = $tarif->nominal ?? 0;

        $today = Carbon::today();

        $borrowings = Borrowing::whereNull('tanggal_kembali')
            ->where('tanggal_jatuh_tempo', '<', $today)
            ->where('status', '!=', 'dikembalikan')
            ->with('user')
            ->get();

        $count = 0;

        foreach ($borrowings as $borrowing) {
            // Update status peminjaman jadi 'terlambat'
            if ($borrowing->status !== 'terlambat') {
                $borrowing->update(['status' => 'terlambat']);
            }

            $hariTelat = Carbon::parse($borrowing->tanggal_jatuh_tempo)->diffInDays($today);
            $nominalDenda = $tarifPerHari * $hariTelat;

            // Cari transaksi denda yang SUDAH ADA untuk peminjaman ini,
            $transaction = Transaction::firstOrNew([
                'borrowing_id' => $borrowing->id,
                'jenis'        => 'denda_keterlambatan',
            ]);

            $transaction->user_id    = $borrowing->user_id;
            $transaction->nominal    = $nominalDenda;
            $transaction->tanggal    = $today;
            $transaction->keterangan = "Denda keterlambatan {$hariTelat} hari (Peminjaman #{$borrowing->id})";

            // Status bayar hanya di-set default saat transaksi baru dibuat,
   
            if (!$transaction->exists) {
                $transaction->status_bayar = 'belum_bayar';
            }

            $transaction->save();
            $count++;
        }

        $this->info("{$count} transaksi denda berhasil diproses.");
    }
}