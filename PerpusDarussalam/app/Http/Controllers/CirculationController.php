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
        // Melakukan pengambilan hasil peminjaman untuk ditampilkan
        $search = $request->query('search');
        $queryBuilder = Borrowing::with(['user', 'book']);

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

        $dbCirculations = $queryBuilder->get();
        
        
        $circulations = $dbCirculations->map(function ($item) {
            return (object)[
                'nis'         => $item->user->nis ?? '-',                 
                'name'        => $item->user->name ?? 'Tanpa Nama',       
                'book_title'  => $item->book->judul ?? 'Buku Terhapus',   
                'borrow_date' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
                'return_date' => $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-'
            ];
        });

        return view('layouts.pages.admin.sirkulasi', compact('circulations', 'search'));
    }

        public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nis_nip'    => 'required', 
            'judul_buku' => 'required',
            'due_date'   => 'required|date',
        ]);

        // Gunakan Transaction agar aman dari race condition
        return DB::transaction(function () use ($request) {
            
            // 2. Cari berdasarkan NIS dan Judul Buku
            $user = User::where('nis', $request->nis_nip)->first();
            // SESUAIKAN: Kolom di database adalah 'judul', bukan 'title'
            $buku = Book::where('judul', $request->judul_buku)->lockForUpdate()->first();

            // 3. Cek apakah data ditemukan
            if (!$user) {
                return back()->withErrors(['error' => 'Anggota dengan NIS tersebut tidak ditemukan!']);
            }
            if (!$buku) {
                return back()->withErrors(['error' => 'Buku dengan judul tersebut tidak ditemukan!']);
            }

            // 4. Cek stok buku
            if ($buku->stok <= 0) {
                return back()->withErrors(['error' => 'Stok buku habis!']);
            }

            $tanggalPinjam = now();
            $tanggalJatuhTempo = $tanggalPinjam->copy()->addWeek(); 

            // 5. Simpan ke database
            Borrowing::create([
                'user_id'             => $user->id,
                'book_id'             => $buku->id,
                'tanggal_pinjam'      => now(),
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'status'              => 'dipinjam'
            ]);

            // 6. Kurangi stok
            $buku->decrement('stok');

            return redirect()->route('sirkulasi.index')->with('success', 'Peminjaman berhasil dicatat!');
        });
    }
}