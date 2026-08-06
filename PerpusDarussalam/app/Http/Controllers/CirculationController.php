<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing; 
use Carbon\Carbon;
use App\Models\User;
use App\Models\BookItem;
use App\Services\BorrowingService;
use App\Services\NotificationService;

class CirculationController extends Controller
{
    public function index(Request $request, NotificationService $notificationService)
    {
        $notificationService->generateLateNotifications();

        $search = $request->query('search');
        $status = $request->query('status');

        // Eager loading relasi secara presisi
        $queryBuilder = Borrowing::with(['user', 'bookItem.book']);

        // 1. Filter Pencarian
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

        // 2. Filter Status (Menggunakan today() agar aman untuk format DATE)
        if ($status) {
            if ($status === 'dipinjam') {
                $queryBuilder->where('status', 'dipinjam')
                            ->whereDate('tanggal_jatuh_tempo', '>=', today());
            } elseif ($status === 'telat') {
                $queryBuilder->where('status', 'dipinjam')
                            ->whereDate('tanggal_jatuh_tempo', '<', today());
            } elseif ($status === 'selesai') {
                $queryBuilder->whereIn('status', ['selesai', 'dikembalikan']);
            }
        }

        // 3. Mapping Data untuk View
        $circulations = $queryBuilder->latest()->paginate(10)->through(function ($item) {
            $isLate = $item->status === 'dipinjam' && Carbon::parse($item->tanggal_jatuh_tempo)->isPast();

            if ($isLate) {
                $statusText = 'Telat';
            } elseif (in_array($item->status, ['dikembalikan', 'selesai'])) {
                $statusText = 'Selesai';
            } else {
                $statusText = 'Peminjaman';
            }

            return (object)[
                'id'          => $item->id,
                'identitas'   => $item->user->nis ?? $item->user->nip ?? $item->user->nik ?? '-',
                'name'        => $item->user->name ?? 'Tanpa Nama',      
                'book_title'  => $item->bookItem->book->judul ?? 'Buku Terhapus',   
                'nomor_inv'   => $item->bookItem->nomor_inventaris ?? '-', 
                'status'      => $statusText,
                'borrow_date' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
                'due_date'    => $item->tanggal_jatuh_tempo ? Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') : '-',
                'return_date' => $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-'
            ];
        })->withQueryString();

        return view('layouts.pages.admin.sirkulasi', compact('circulations', 'search', 'status'));
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
                ], 'borrowForm') // <-- Berikan nama error bag khusus 'borrowForm'
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

    public function getBookByInventory($nomorInventaris)
    {
        // Mengambil data book_item beserta relasi ke tabel book untuk mendapatkan judulnya
        $bookItem = BookItem::with('book')
                    ->where('nomor_inventaris', $nomorInventaris)
                    ->orWhere('id', $nomorInventaris) // Mengantisipasi jika yang diinput ID-nya
                    ->first();

        if ($bookItem && $bookItem->book) {
            return response()->json([
                'success' => true,
                'title' => $bookItem->book->judul,
                'status' => $bookItem->status_pinjam,
                'kondisi' => $bookItem->kondisi
            ]);
        }

        return response()->json([
            'success' => false,
            'title' => 'Buku tidak ditemukan'
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