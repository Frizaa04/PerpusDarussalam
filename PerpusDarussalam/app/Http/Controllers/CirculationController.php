<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing; 
use Carbon\Carbon;
use App\Models\User;
use App\Services\BorrowingService;
use App\Services\NotificationService;

class CirculationController extends Controller
{
    public function index(Request $request,NotificationService $notificationService)
    {
        $notificationService
            ->generateLateNotifications();

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

    public function store(Request $request, BorrowingService $service)
{
    $request->validate([
        'identitas' => 'required',
        'book_item_id' => 'required',
        'tanggal_pinjam' => 'nullable|date',
    ]);

    try {

        $service->borrow($request->all());

        return redirect()
            ->route('circulation.index')
            ->with('success', 'Peminjaman berhasil.');

    } catch (\Exception $e) {

        return back()
            ->withErrors([
                'error' => $e->getMessage()
            ])
            ->withInput();

    }
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

    public function returnBook($id, BorrowingService $service)
    {
        $service->returnBook($id);

        return redirect()
            ->route('circulation.index')
            ->with('success','Buku berhasil dikembalikan');
    }

    public function cancelBorrow($id, BorrowingService $service)
    {
        $service->cancelBorrow($id);

        return back()->with(
            'success',
            'Peminjaman dibatalkan.'
        );
    }
}