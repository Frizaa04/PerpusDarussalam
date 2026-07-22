<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing; 
use Carbon\Carbon;
use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $lateOnly = $request->query('late'); // Filter peminjaman telat

        $queryBuilder = Borrowing::with(['user', 'book']);

        // Filter Pencarian
        if ($search) {
            $queryBuilder->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('nis', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('book', function($bookQuery) use ($search) {
                    $bookQuery->where('judul', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filter Hanya Peminjaman Telat
        if ($lateOnly) {
            $queryBuilder->where('status', 'dipinjam')
                        ->where('tanggal_jatuh_tempo', '<', now());
        }

        $dbCirculations = $queryBuilder->latest()->get();

        $circulations = $dbCirculations->map(function ($item) {
            // Logika Penentuan Status
            $status = $item->status ?? 'Peminjaman';
            if ($item->status === 'dipinjam' && Carbon::parse($item->tanggal_jatuh_tempo)->isPast()) {
                $status = 'Telat';
            } elseif ($item->status === 'dikembalikan') {
                $status = 'Selesai';
            } else {
                $status = 'Peminjaman';
            }

            return (object)[
                'id'          => $item->id,
                'nis'         => $item->user->nis ?? '-',                 
                'name'        => $item->user->name ?? 'Tanpa Nama',       
                'book_title'  => $item->book->judul ?? 'Buku Terhapus',   
                'status'      => $status,
                'borrow_date' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
                'return_date' => $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-'
            ];
        });

        return view('layouts.pages.admin.sirkulasi', compact('circulations', 'search', 'lateOnly'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis_nip'    => 'required', 
            'judul_buku' => 'required',
            // Sesuaikan nama field dengan yang dikirim dari form HTML Anda, 
            // misal: 'tanggal_pinjam' atau 'due_date'
            'tanggal_pinjam' => 'nullable|date', 
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::where('nis', $request->nis_nip)->first();
            $buku = Book::where('judul', $request->judul_buku)->lockForUpdate()->first();

            if (!$user) {
                return back()->withErrors(['error' => 'Anggota dengan NIS tersebut tidak ditemukan!'])->withInput();
            }
            if (!$buku) {
                return back()->withErrors(['error' => 'Buku dengan judul tersebut tidak ditemukan!'])->withInput();
            }

            if ($buku->stok <= 0) {
                return back()->withErrors(['error' => 'Stok buku habis!'])->withInput();
            }

            // Tentukan tanggal pinjam (jika kosong diisi hari ini)
            $tanggalPinjam = $request->tanggal_pinjam ? Carbon::parse($request->tanggal_pinjam) : now();
            
            // Tentukan jatuh tempo otomatis 1 minggu (7 hari) dari tanggal pinjam
            $tanggalJatuhTempo = $tanggalPinjam->copy()->addDays(7); 

            Borrowing::create([
                'user_id'             => $user->id,
                'book_id'             => $buku->id,
                'tanggal_pinjam'      => $tanggalPinjam,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'status'              => 'dipinjam'
            ]);

            $buku->decrement('stok');

            return redirect()->route('circulation.index')->with('success', 'Peminjaman berhasil dicatat!');
        });
    }

    public function returnBook($id)
    {
        return DB::transaction(function () use ($id) {
            $borrowing = Borrowing::with('book')->findOrFail($id);

            if ($borrowing->status === 'dipinjam' || $borrowing->status === 'terlambat') {
                // Perbarui status dan catat tanggal kembali hari ini
                $borrowing->update([
                    'status' => 'dikembalikan', 
                    'tanggal_kembali' => now(),
                ]);

                // Kembalikan stok buku
                if ($borrowing->book) {
                    $borrowing->book->increment('stok');
                }
            }

            return redirect()->route('circulation.index')->with('success', 'Buku berhasil dikembalikan!');
        });
    }

    public function cancelBorrow($id)
    {
        DB::transaction(function () use ($id) {

            $borrowing = Borrowing::findOrFail($id);

            if ($borrowing->status == 'dipinjam') {

                $borrowing->book->increment('stok');

                $borrowing->delete();
                // atau
                // $borrowing->status = 'dibatalkan';
                // $borrowing->save();
            }

        });

        return back()->with('success','Peminjaman dibatalkan.');
    }
}
