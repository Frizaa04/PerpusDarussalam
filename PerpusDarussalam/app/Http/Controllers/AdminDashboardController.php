<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\visits;       
use App\Models\Borrowing;    
// use App\Models\ActivityLog; 

class AdminDashboardController extends Controller
{
    public function index(){
        // 
        $todayVisitors = visits::where('created_at', '>=', today()->startOfDay())
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

        // Data dummy aktivitas terbaru (seperti sebelumnya)
        $recentActivities = [
            ['waktu' => '14:20', 'tindakan' => 'Peminjaman', 'detail_buku' => 'Algoritma dan Struktur Data', 'user' => 'Febri Hamzah Jemikan Nata'],
            ['waktu' => '11:10', 'tindakan' => 'Pengembalian', 'detail_buku' => 'Basis Data Pemula', 'user' => 'Muhamad Aditya Nugroho'],
            ['waktu' => '08:10', 'tindakan' => 'Tambah Buku Baru', 'detail_buku' => 'Ivanna', 'user' => 'Admin'],
            ['waktu' => '07:30', 'tindakan' => 'Tambah Stok', 'detail_buku' => 'One Punch Man', 'user' => 'Admin'],
        ];

        return view('layouts.pages.admin.dashboard', compact(
            'todayVisitors', 
            'todayBorrowings', 
            'todayReturns', 
            'recentActivities'
        ));
    }
}