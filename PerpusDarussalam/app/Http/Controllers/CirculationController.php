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
            'due_date'   => 'nullable|date',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::where('nis', $request->nis_nip)->first();
            $buku = Book::where('judul', $request->judul_buku)->lockForUpdate()->first();

            if (!$user) {
                return back()->withErrors(['error' => 'Anggota dengan NIS tersebut tidak ditemukan!']);
            }
            if (!$buku) {
                return back()->withErrors(['error' => 'Buku dengan judul tersebut tidak ditemukan!']);
            }

            if ($buku->stok <= 0) {
                return back()->withErrors(['error' => 'Stok buku habis!']);
            }

            $tanggalPinjam = $request->due_date ? Carbon::parse($request->due_date) : now();
            $tanggalJatuhTempo = $tanggalPinjam->copy()->addWeek(); 

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
}