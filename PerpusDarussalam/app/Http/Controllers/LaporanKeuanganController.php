<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tanggal yang dipilih atau default hari ini
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        // Membina daftar tanggal untuk filter horizontal (misal: 7 hari terakhir/rentang tertentu)
        $dates = [];
        $baseDate = Carbon::parse($selectedDate);
        
        // Mengambil 6 hari sebelumnya + hari ini (total 7 tanggal seperti di UI)
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = [
                'day' => $baseDate->copy()->subDays($i)->format('d'),
                'full_date' => $baseDate->copy()->subDays($i)->format('Y-m-d'),
                'is_active' => $baseDate->copy()->subDays($i)->format('Y-m-d') === $selectedDate
            ];
        }

        // Logic ringkasan keuangan berdasarkan tanggal
        // Disesuaikan dengan query database Anda (misal dari tabel transaksi/denda)
        $pembuatanKartu = 0; // Contoh: Transaksi::where('tipe', 'pembuatan_kartu')->whereDate('created_at', $selectedDate)->sum('jumlah');
        $kehilanganKartu = 0; // Contoh: Transaksi::where('tipe', 'kehilangan_kartu')->whereDate('created_at', $selectedDate)->sum('jumlah');
        $keterlambatanBuku = 0; // Contoh: Denda::whereDate('created_at', $selectedDate)->sum('jumlah');

        return view('layouts.pages.admin.laporan_keuangan', compact(
            'dates', 
            'selectedDate', 
            'pembuatanKartu', 
            'kehilanganKartu', 
            'keterlambatanBuku'
        ));
    }
}