<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Notification;

class NotificationService
{
    public function generateLateNotifications()
    {

        $lateBorrowings = Borrowing::with(['user', 'bookItem.book'])
                ->where('status', 'dipinjam')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->get();

            foreach ($lateBorrowings as $late) {
                $userName = $late->user->name ?? 'Tanpa Nama';
                $bookTitle = $late->bookItem->book->judul ?? 'Buku';

                // firstOrCreate akan mengecek: 
                // Jika borrowing_id dan type ini sudah ada, jangan buat baru (hindari duplikat).
                // Jika belum ada, buat notifikasi baru dengan data di array kedua.
                Notification::firstOrCreate(
                    [
                        'borrowing_id' => $late->id,
                        'type'         => 'keterlambatan' // Tipe notifikasi
                    ],
                    [
                        'title'   => 'Peringatan: Keterlambatan Pengembalian',
                        'message' => "Peminjaman buku '{$bookTitle}' oleh {$userName} telah melewati batas waktu pengembalian.",
                        'status'  => 'unread' // Status default saat notifikasi masuk
                    ]
                );
            }

    }
}
