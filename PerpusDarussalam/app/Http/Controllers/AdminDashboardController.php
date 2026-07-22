<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\visits; 
use App\Models\Borrowing; 
use Carbon\Carbon;   

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Menghitung pengunjung hari ini
        $todayVisitors = visits::where('created_at', '>=', today()->startOfDay())
                               ->where('created_at', '<=', today()->endOfDay())
                               ->count();
        
        // Menghitung peminjaman buku hari ini
        $todayBorrowings = Borrowing::query()
                                    ->where('status', 'dipinjam')
                                    ->whereDate('created_at', today())
                                    ->count();
                                    
        // Menghitung pengembalian buku hari ini
        $todayReturns = Borrowing::query()
                                  ->where('status', 'dikembalikan')
                                  ->whereDate('updated_at', today())
                                  ->count();

        // Ambil data transaksi peminjaman & pengembalian terbaru langsung dari Database
        $recentActivities = Borrowing::with(['user', 'book'])
            ->latest('updated_at') 
            ->take(5)              
            ->get()
            ->map(function ($item) {
                // Jika statusnya dikembalikan, tindakan = Pengembalian & waktu diambil dari updated_at
                $isReturn = $item->status === 'dikembalikan';
                $time = $isReturn ? $item->updated_at : $item->created_at;

                return [
                    'waktu'       => Carbon::parse($time)->format('H:i'),
                    'tindakan'    => $isReturn ? 'Pengembalian' : 'Peminjaman',
                    'detail_buku' => $item->book->judul ?? 'Buku Terhapus',
                    'user'        => $item->user->name ?? 'Tanpa Nama',
                ];
            });

        return view('layouts.pages.admin.dashboard', compact(
            'todayVisitors', 
            'todayBorrowings', 
            'todayReturns', 
            'recentActivities'
        ));
    }
}