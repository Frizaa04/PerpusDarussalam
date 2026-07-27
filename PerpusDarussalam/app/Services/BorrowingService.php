<?php

namespace App\Services;

use App\Models\User;
use App\Models\BookItem;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    public function borrow(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = User::where('nis', $data['identitas'])
                ->orWhere('nip', $data['identitas'])
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

            $borrowing = Borrowing::with('bookItem')->findOrFail($id);

            if ($borrowing->status === 'dipinjam') {

                $borrowing->update([
                    'status'=>'dikembalikan',
                    'tanggal_kembali'=>now()
                ]);

                if($borrowing->bookItem){

                    $borrowing->bookItem->update([
                        'status_pinjam'=>'tersedia'
                    ]);

                }

            }

        });
    }

    public function cancelBorrow($id)
    {
        return DB::transaction(function () use ($id){

            $borrowing = Borrowing::with('bookItem')->findOrFail($id);

            if($borrowing->status=="dipinjam"){

                if($borrowing->bookItem){

                    $borrowing->bookItem->update([
                        'status_pinjam'=>'tersedia'
                    ]);

                }

                $borrowing->delete();

            }

        });

    }

    
}