<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing; 
use Carbon\Carbon;

class CirculationController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');

        $queryBuilder = Borrowing::with(['user', 'book']);

        if ($search) {
            $queryBuilder->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('nis', 'LIKE', "%{$search}%"); // Mengasumsikan ada kolom 'nis' di tabel users lu
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
}