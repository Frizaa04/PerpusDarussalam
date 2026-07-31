<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\visits; 
use App\Models\Borrowing;
use App\Models\BookItem;
use App\Models\User; 
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
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        
        // Batasi sampai hari ini saja agar hari esok/masa depan tidak tampil 0 di grafik
        $endOfData = $now->copy(); 

        $borrowingsData = Borrowing::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startOfWeek, $now->copy()->endOfDay()])
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $chartPeminjaman = [];

        // Hitung selisih hari dari Senin sampai hari ini (maksimal 7 hari)
        $daysCount = $startOfWeek->diffInDays($now) + 1;
        if ($daysCount > 7) $daysCount = 7;

        for ($i = 0; $i < $daysCount; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('D, d M');
            $chartPeminjaman[] = (int) ($borrowingsData[$dateStr] ?? 0);
        }

        $chartLabels = $labels;

        // Aktivitas Terbaru
        $recentActivities = Borrowing::with(['user', 'bookItem.book'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $isReturn = $item->status === 'dikembalikan';
                $time = $isReturn ? $item->updated_at : $item->created_at;

                return [
                    'waktu'       => Carbon::parse($time)->format('H:i'),
                    'tindakan'    => $isReturn ? 'Pengembalian' : 'Peminjaman',
                    'detail_buku' => $item->bookItem->book->judul ?? 'Buku Terhapus',
                    'user'        => $item->user->name ?? 'Tanpa Nama',
                ];
            });

        return view('layouts.pages.admin.dashboard', compact(
            'todayVisitors', 
            'todayBorrowings', 
            'todayReturns', 
            'totalMembers',
            'totalBookItems',
            'recentActivities',
            'chartPeminjaman',
            'chartLabels'
        ));
    }
}