<?php

namespace App\Services;

use App\Models\User;
use App\Models\BookItem;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Tarif;

class BorrowingService
{
    protected const ACTIVE_STATUSES = ['dipinjam', 'terlambat'];

    public function borrow(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = User::where('nisn', $data['identitas'])
                ->orWhere('nik', $data['identitas'])
                ->first();

            if (!$user) {
                throw new \Exception('Anggota tidak ditemukan');
            }

            $bookItem = BookItem::where('nomor_inventaris', $data['book_item_id'])
                ->orWhere('id', $data['book_item_id'])
                ->lockForUpdate()
                ->first();

            if (!$bookItem) {
                throw new \Exception('Buku tidak ditemukan');
            }

            if ($bookItem->status_pinjam === 'dipinjam') {
                throw new \Exception('Buku sedang dipinjam');
            }

            if ($bookItem->kondisi === 'rusak_berat') {
                throw new \Exception('Buku rusak berat');
            }

            $tanggalPinjam = isset($data['tanggal_pinjam'])
                ? Carbon::parse($data['tanggal_pinjam'])
                : now();

            $borrowing = Borrowing::create([
                'user_id' => $user->id,
                'book_item_id' => $bookItem->id,
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_jatuh_tempo' => $tanggalPinjam->copy()->addDays(7),
                'status' => 'dipinjam',
            ]);

            $bookItem->update([
                'status_pinjam' => 'dipinjam'
            ]);

            return $borrowing;
        });
    }

    public function returnBook($id)
    {
        return DB::transaction(function () use ($id) {

            $borrowing = Borrowing::with(['bookItem', 'user'])->findOrFail($id);

            // Status bisa 'dipinjam' ATAU 'terlambat'
            if (!in_array($borrowing->status, self::ACTIVE_STATUSES, true)) {
                throw new \Exception('Peminjaman ini sudah tidak aktif, tidak bisa dikembalikan.');
            }

            $tanggalKembali = now();

            $borrowing->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => $tanggalKembali
            ]);

            if ($borrowing->bookItem) {
                $borrowing->bookItem->update([
                    'status_pinjam' => 'tersedia'
                ]);
            }

            // Cek keterlambatan, buat transaksi denda otomatis
            $jatuhTempo = Carbon::parse($borrowing->tanggal_jatuh_tempo)->startOfDay();
            $kembali = $tanggalKembali->copy()->startOfDay();

            if ($kembali->greaterThan($jatuhTempo)) {
                $hariTelat = $jatuhTempo->diffInDays($kembali);

                $tarifPerHari = Tarif::where('jenis', 'denda_keterlambatan')->value('nominal') ?? 0;
                $nominalDenda = $hariTelat * $tarifPerHari;

                Transaction::create([
                    'user_id'      => $borrowing->user_id,
                    'name'         => $borrowing->user->name ?? 'Non-Anggota',
                    'jenis'        => 'denda_keterlambatan',
                    'nominal'      => $nominalDenda,
                    'tanggal'      => $tanggalKembali->toDateString(),
                    'keterangan'   => "Denda keterlambatan {$hariTelat} hari (Peminjaman #{$borrowing->id})",
                    'status_bayar' => 'belum_bayar',
                ]);
            }
        });
    }

    public function cancelBorrow($id)
    {
        return DB::transaction(function () use ($id) {

            $borrowing = Borrowing::with('bookItem')->findOrFail($id);

            // Status bisa 'dipinjam' ATAU 'terlambat'
            if (in_array($borrowing->status, self::ACTIVE_STATUSES, true)) {

                if ($borrowing->bookItem) {
                    $borrowing->bookItem->update([
                        'status_pinjam' => 'tersedia'
                    ]);
                }

                $borrowing->delete();
            }
        });
    }

    // Tandai buku hilang + buat transaksi ganti rugi otomatis
    public function reportLost($id)
    {
        return DB::transaction(function () use ($id) {

            $borrowing = Borrowing::with(['bookItem.book', 'user'])->findOrFail($id);

            // Status bisa 'dipinjam' ATAU 'terlambat'
            if (!in_array($borrowing->status, self::ACTIVE_STATUSES, true)) {
                throw new \Exception('Peminjaman ini sudah tidak aktif, tidak bisa ditandai hilang.');
            }

            $borrowing->update([
                'status'         => 'hilang',
                'tanggal_kembali' => now(),
            ]);

            if ($borrowing->bookItem) {
                $borrowing->bookItem->update([
                    'status_pinjam' => 'hilang'
                ]);
            }

            $tarifHilang = Tarif::where('jenis', 'kehilangan_buku')->value('nominal') ?? 0;
            $judulBuku   = $borrowing->bookItem->book->judul ?? 'Buku';

            Transaction::create([
                'user_id'      => $borrowing->user_id,
                'name'         => $borrowing->user->name ?? 'Non-Anggota',
                'jenis'        => 'kehilangan_buku',
                'nominal'      => $tarifHilang,
                'tanggal'      => now()->toDateString(),
                'keterangan'   => "Kehilangan buku: {$judulBuku} (Peminjaman #{$borrowing->id})",
                'status_bayar' => 'belum_bayar',
            ]);

            return $borrowing;
        });
    }

}