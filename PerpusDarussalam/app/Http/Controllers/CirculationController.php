<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing; 
use Carbon\Carbon;
use App\Models\User;
use App\Models\BookItem;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $lateOnly = $request->query('late');

        $queryBuilder = Borrowing::with(['user', 'bookItem.book']);

        // Filter Pencarian
        if ($search) {
            $queryBuilder->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('nis', 'LIKE', "%{$search}%")
                              ->orWhere('nip', 'LIKE', "%{$search}%")
                              ->orWhere('nik', 'LIKE', "%{$search}%");
                                    })
                ->orWhereHas('bookItem.book', function($bookQuery) use ($search) {
                    $bookQuery->where('judul', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('bookItem', function($itemQuery) use ($search) {
                    $itemQuery->where('nomor_inventaris', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filter hanya menampilkan peminjaman yang telat
        if ($lateOnly) {
            $queryBuilder->where('status', 'dipinjam')
                         ->where('tanggal_jatuh_tempo', '<', now());
        }

        $dbCirculations = $queryBuilder->latest()->get();

        $circulations = $dbCirculations->map(function ($item) {
            $status = $item->status ?? 'Peminjaman';
            if ($item->status === 'dipinjam' && Carbon::parse($item->tanggal_jatuh_tempo)->isPast()) {
                $status = 'Telat';
            } elseif ($item->status === 'dikembalikan') {
                $status = 'Selesai';
            } else {
                $status = 'Peminjaman';
            }

            return (object)[
                'id'            => $item->id,
                'identitas'     => $item->user->nis ?? $item->user->nip ?? $item->user->nik ?? '-',
                'name'          => $item->user->name ?? 'Tanpa Nama',       
                'book_title'    => $item->bookItem->book->judul ?? 'Buku Terhapus',   
                'nomor_inv'     => $item->bookItem->nomor_inventaris ?? '-', 
                'status'        => $status,
                'borrow_date'   => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
                'due_date'      => $item->tanggal_jatuh_tempo ? Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') : '-',
                'return_date'   => $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-'
            ];
        });

        return view('layouts.pages.admin.sirkulasi', compact('circulations', 'search', 'lateOnly'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'identitas'          => 'required', 
            'book_item_id'     => 'required', 
            'tanggal_pinjam'   => 'nullable|date', 
        ]);

        return DB::transaction(function () use ($request) {
            // Cari user secara fleksibel (NIS, NIP, atau NIK)
            $user = User::where('nis', $request->identitas)
                        ->orWhere('nip', $request->identitas)
                        ->orWhere('nik', $request->identitas)
                        ->first();

            // Cari item buku berdasarkan nomor inventaris fisik
            $bookItem = BookItem::where('nomor_inventaris', $request->book_item_id)
                                ->orWhere('id', $request->book_item_id)
                                ->lockForUpdate()
                                ->first();

            if (!$user) {
                return back()->withErrors(['error' => 'Anggota (NIS/NIP/NIK) tidak ditemukan!'])->withInput();
            }
            if (!$bookItem) {
                return back()->withErrors(['error' => 'Nomor Inventaris Buku tidak ditemukan!'])->withInput();
            }

            // Validasi apakah buku fisik sedang dipinjam
            if ($bookItem->status_pinjam === 'dipinjam') {
                return back()->withErrors(['error' => 'Eksemplar buku ini sedang dipinjam oleh anggota lain!'])->withInput();
            }

            // Validasi kondisi fisik buku
            if ($bookItem->kondisi === 'rusak_berat') {
                return back()->withErrors(['error' => 'Buku ini berstatus rusak berat dan tidak layak dipinjamkan!'])->withInput();
            }

            $tanggalPinjam = $request->tanggal_pinjam ? Carbon::parse($request->tanggal_pinjam) : now();
            $tanggalJatuhTempo = $tanggalPinjam->copy()->addDays(7); 

            // Catat peminjaman
            Borrowing::create([
                'user_id'             => $user->id,
                'book_item_id'        => $bookItem->id, 
                'tanggal_pinjam'      => $tanggalPinjam,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'status'              => 'dipinjam'
            ]);

            // Ubah status fisik buku menjadi dipinjam
            $bookItem->update([
                'status_pinjam' => 'dipinjam'
            ]);

            return redirect()->route('circulation.index')->with('success', 'Peminjaman berhasil dicatat!');
        });
    }

    public function getUserByNikNisNip($nomor)
    {
        $user = User::where('nis', $nomor)
                    ->orWhere('nip', $nomor)
                    ->orWhere('nik', $nomor)
                    ->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'name' => $user->name
            ]);
        }

        return response()->json([
            'success' => false,
            'name' => 'Anggota tidak ditemukan'
        ]);
    }

    public function returnBook($id)
    {
        return DB::transaction(function () use ($id) {
            $borrowing = Borrowing::with('bookItem')->findOrFail($id);

            if ($borrowing->status === 'dipinjam') {
                // Perbarui status peminjaman
                $borrowing->update([
                    'status' => 'dikembalikan', 
                    'tanggal_kembali' => now(),
                ]);

                // Kembalikan status item fisik buku menjadi tersedia
                if ($borrowing->bookItem) {
                    $borrowing->bookItem->update([
                        'status_pinjam' => 'tersedia'
                    ]);
                }
            }

            return redirect()->route('circulation.index')->with('success', 'Buku berhasil dikembalikan!');
        });
    }

    public function cancelBorrow($id)
    {
        return DB::transaction(function () use ($id) {
            $borrowing = Borrowing::with('bookItem')->findOrFail($id);

            if ($borrowing->status === 'dipinjam') {
                // Kembalikan status item fisik buku menjadi tersedia
                if ($borrowing->bookItem) {
                    $borrowing->bookItem->update([
                        'status_pinjam' => 'tersedia'
                    ]);
                }

                $borrowing->delete();
            }

            return back()->with('success', 'Peminjaman dibatalkan.');
        });
    }
}