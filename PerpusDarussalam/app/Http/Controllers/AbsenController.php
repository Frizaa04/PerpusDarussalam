<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visits;

class AbsenController extends Controller
{
    public function index()
    {
        // Mengambil data visits beserta relasi user
        // Diurutkan dari kunjungan terbaru
        $visits = Visits::with('user')
            ->latest('visited_at')
            ->get();

        return view('layouts.pages.admin.absen', compact('visits'));
    }
}