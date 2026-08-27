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

        $borrowingsData = Borrowing::with(['user', 'bookItem.book'])
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $now->copy()->endOfDay()
            ])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $labels = [];
        $chartPeminjaman = [];
        $chartDetails = [];
        $chartDates = [];

        // Looping tepat 7 hari ke belakang hingga hari ini
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $chartDates[] = $dateStr;
            $labels[] = $date->translatedFormat('D, d M');

            // Ambil semua peminjaman pada tanggal tersebut
            $dailyBorrowings = $borrowingsData->get($dateStr, collect());

            // Jumlah peminjaman
            $chartPeminjaman[] = $dailyBorrowings->count();

            // Detail untuk tooltip (berisi nama peminjam dan judul buku)
            $chartDetails[$dateStr] = $dailyBorrowings->map(function ($borrowing) {
                return [
                    'peminjam' => $borrowing->user->name ?? 'Tanpa Nama',
                    'buku'     => $borrowing->bookItem->book->judul ?? 'Buku Terhapus',
                ];
            })->values()->toArray();
        }

        $chartLabels = $labels;

        // Aktivitas Terbaru (Diperbaiki agar log Peminjaman dan Pengembalian terpisah)
        $borrowingsForActivity = Borrowing::with(['user', 'bookItem.book'])
            ->latest('updated_at')
            ->take(50)
            ->get();

        $allActivities = collect();

        foreach ($borrowingsForActivity as $item) {
            $namaBuku = $item->bookItem->book->judul ?? 'Buku Terhapus';
            $namaUser = $item->user->name ?? 'Tanpa Nama';

            // 1. Catat log Peminjaman (berdasarkan waktu created_at)
            $allActivities->push([
                'timestamp'   => Carbon::parse($item->created_at)->timestamp,
                'tanggal'     => Carbon::parse($item->created_at)->format('d M Y'),
                'waktu'       => Carbon::parse($item->created_at)->format('H:i'),
                'tindakan'    => 'Peminjaman',
                'detail_buku' => $namaBuku,
                'user'        => $namaUser,
            ]);

            // 2. Jika statusnya sudah dikembalikan, buat catatan log Pengembalian terpisah (berdasarkan updated_at)
            if ($item->status === 'dikembalikan') {
                $allActivities->push([
                    'timestamp'   => Carbon::parse($item->updated_at)->timestamp,
                    'tanggal'     => Carbon::parse($item->updated_at)->format('d M Y'),
                    'waktu'       => Carbon::parse($item->updated_at)->format('H:i'),
                    'tindakan'    => 'Pengembalian',
                    'detail_buku' => $namaBuku,
                    'user'        => $namaUser,
                ]);
            }
        }

        // Urutkan ulang log berdasarkan waktu terbaru secara global
        $allActivities = $allActivities->sortByDesc('timestamp')->values();

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('activity_page');
        $currentItems = $allActivities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $recentActivities = new LengthAwarePaginator(
            $currentItems,
            $allActivities->count(),
            $perPage,
            $currentPage,
            [
                'path'     => LengthAwarePaginator::resolveCurrentPath(),
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
            'chartDetails',
            'chartDates',
            'recentTransactions',
            'totalNominalTransaksi'
        ));
    }
}