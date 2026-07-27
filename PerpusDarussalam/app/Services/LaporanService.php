<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Visits;
use App\Models\Borrowing;
use Carbon\Carbon;

class LaporanService
{
    public function dashboard(Carbon $tanggal)
    {
        return [
            'totalKoleksi' => Book::sum('stok'),
            'totalAnggota' => User::count(),
            'pengunjung' => Visits::whereDate(
                'visited_at',
                $tanggal
            )->count(),
            'bukuBaru' => Book::whereDate(
                'created_at',
                $tanggal
            )->count(),
            'peminjaman' => Borrowing::whereDate(
                'created_at',
                $tanggal
            )->count(),
            'pengembalian' => Borrowing::where('status','dikembalikan')
                ->whereDate('updated_at',$tanggal)
                ->count()
        ];
    }

    public function koleksi()
    {
        return [
            'totalKoleksi' => Book::sum('stok'),
            'totalJudulBukuFisik' => Book::count(),
            'totalEbook' => 0,
            'totalStokBukuFisik' => Book::sum('stok'),
            'kategoriReferensi' => Book::whereHas('categories',function($q){
                $q->where('nama','Referensi');
            })->sum('stok'),

            'kategoriBacaan' => Book::whereHas('categories',function($q){
                $q->where('nama','Bacaan');
            })->sum('stok')
        ];
    }

    public function anggota()
    {
        return [
            'totalAnggota' => User::count(),
            'lakiLaki' => User::where('jenis_kelamin','L')->count(),
            'perempuan' => User::where('jenis_kelamin','P')->count(),
            'siswa' => User::where('role','siswa')->count(),
            'guru' => User::where('role','guru')->count(),
            'umum' => User::where('role','umum')->count()
        ];
    }

    public function pengunjung(Carbon $tanggal)
    {
        $query = Visits::whereDate('visited_at',$tanggal);
        return [
            'totalPengunjung' => (clone $query)->count(),
            'lakiLaki' => (clone $query)
                ->whereHas('user',fn($q)=>$q->where('jenis_kelamin','L'))
                ->count(),
            'perempuan' => (clone $query)
                ->whereHas('user',fn($q)=>$q->where('jenis_kelamin','P'))
                ->count(),
            'siswa' => (clone $query)
                ->whereHas('user',fn($q)=>$q->where('role','siswa'))
                ->count(),
            'guru' => (clone $query)
                ->whereHas('user',fn($q)=>$q->where('role','guru'))
                ->count(),
            'umum' => (clone $query)
                ->whereHas('user',fn($q)=>$q->where('role','umum'))
                ->count(),
        ];
    }

    public function dates(Carbon $selectedDate)
    {
        $dates = [];
        for($i=6;$i>=0;$i--){
            $date = today()->subDays($i);
            $dates[]=[
                'day'=>$date->format('d'),
                'full_date'=>$date->format('Y-m-d'),
                'is_active'=>$date->isSameDay($selectedDate)
            ];
        }
        return $dates;
    }
}