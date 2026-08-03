<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\visits; 
use App\Models\Borrowing;
use App\Models\BookItem;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;   

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistik Ringkasan Hari Ini
        $todayVisitors = Visits::where('created_at', '>=', today()->startOfDay())
                            ->where('created_at', '<=', today()->endOfDay())
                            ->count();
        
        $todayBorrowings = Borrowing::query()
                                    ->where('status', 'dipinjam')
                                    ->whereDate('created_at', today())
                                    ->count();
                                    
        $todayReturns = Borrowing::query()
                                ->where('status', 'dikembalikan')
                                ->whereDate('updated_at', today())
                                ->count();

        $totalMembers = User::count();

        $totalBookItems = BookItem::count();

        $now = Carbon::now();

        // Ambil rentang 6 hari ke belakang sampai hari ini (total tepat 7 hari / 1 minggu penuh)
        $startDate = $now->copy()->subDays(6); 

        $borrowingsData = Borrowing::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $now->copy()->endOfDay()])
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $chartPeminjaman = [];

        // Looping tepat 7 hari ke belakang hingga hari ini
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('D, d M');
            $chartPeminjaman[] = (int) ($borrowingsData[$dateStr] ?? 0);
        }

        $chartLabels = $labels;

        // Aktivitas Terbaru
       $allActivities = Borrowing::with(['user', 'bookItem.book'])
            ->latest('updated_at')
            ->take(50)
            ->get()
            ->map(function ($item) {
                $isReturn = $item->status === 'dikembalikan';
                $time = $isReturn ? $item->updated_at : $item->created_at;

                return [
                    'tanggal'     => Carbon::parse($time)->format('d M Y'), // Ditambahkan untuk kolom tanggal
                    'waktu'       => Carbon::parse($time)->format('H:i'),
                    'tindakan'    => $isReturn ? 'Pengembalian' : 'Peminjaman',
                    'detail_buku' => $item->bookItem->book->judul ?? 'Buku Terhapus',
                    'user'        => $item->user->name ?? 'Tanpa Nama',
                ];
            });

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('activity_page'); // Gunakan parameter kustom agar tidak bentrok dengan paginator lain jika ada
        $currentItems = $allActivities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $recentActivities = new LengthAwarePaginator(
            $currentItems,
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'activity_page',
            ]
        );

        $recentTransactions = Transaction::with('user')
            ->latest('tanggal')
            ->paginate(5, ['*'], 'transaction_page');

        $totalNominalTransaksi = Transaction::sum('nominal');

        return view('layouts.pages.admin.dashboard', compact(
            'todayVisitors', 
            'todayBorrowings', 
            'todayReturns', 
            'totalMembers',
            'totalBookItems',
            'recentActivities',
            'chartPeminjaman',
            'chartLabels',
            'recentTransactions',
            'totalNominalTransaksi'
        ));
    }
}