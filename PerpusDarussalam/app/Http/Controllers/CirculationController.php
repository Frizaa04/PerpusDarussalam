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
                            ->orWhere('nisn', 'LIKE', "%{$search}%")
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

        // 2. Filter Status     
        if ($status) {
            if ($status === 'dipinjam') {
                $queryBuilder->where('status', 'dipinjam')
                            ->whereDate('tanggal_jatuh_tempo', '>=', today());
            } elseif ($status === 'telat') {
                $queryBuilder->where('status', 'dipinjam')
                            ->whereDate('tanggal_jatuh_tempo', '<', today());
            } elseif ($status === 'selesai') {
                $queryBuilder->whereIn('status', ['selesai', 'dikembalikan']);
            } elseif ($status === 'hilang') {
                $queryBuilder->where('status', 'hilang');
            }
        }
        

        // 3. Mapping Data untuk View
        $circulations = $queryBuilder->latest()->paginate(10)->through(function ($item) {
            $isLate = $item->status === 'dipinjam' && Carbon::parse($item->tanggal_jatuh_tempo)->isPast();

            $statusText = match(true) {
                $item->status === 'terlambat' => 'Telat',
                $item->status === 'hilang' => 'Hilang',
                in_array($item->status, ['dikembalikan', 'selesai']) => 'Selesai',
                default => 'Peminjaman',
            };

            return (object)[
                'id'          => $item->id,
                'identitas'   => $item->user->nisn ?? $item->user->nik ?? '-', 
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
                ], 'borrowForm') 
                ->withInput();
        }
    }

    public function getUserByNikNisn($nomor)
    {
        $user = User::where('nisn', $nomor)
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
        $bookItem = BookItem::with('book')
                    ->where('nomor_inventaris', $nomorInventaris)
                    ->orWhere('id', $nomorInventaris) 
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

    public function loseBook($id, BorrowingService $service)
    {
        try {
            $service->reportLost($id);

            return redirect()
                ->route('circulation.index')
                ->with('success', 'Buku ditandai hilang, transaksi ganti rugi otomatis dibuat.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}