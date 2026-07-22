<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController; 
use App\Http\Controllers\AbsenController; // Import AbsenController

// Dashboard Admin
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Manajemen Siswa / User
Route::get('/manajemen-siswa', [MemberController::class, 'index'])->name('member.index');
Route::put('/manajemen-siswa/update', [MemberController::class, 'update'])->name('member.update');

// Katalog Buku
Route::get('/katalog-buku', [BookController::class, 'index'])->name('book.index');
Route::post('/katalog-buku/store', [BookController::class, 'store'])->name('book.store');
Route::put('/katalog-buku/update', [BookController::class, 'update'])->name('book.update');

// Sirkulasi
Route::get('/sirkulasi', [CirculationController::class, 'index'])->name('circulation.index');
Route::post('/sirkulasi', [CirculationController::class, 'store'])->name('circulation.store');
Route::post('/sirkulasi/return/{id}', [CirculationController::class, 'returnBook'])->name('circulation.return');
Route::post('/circulation/cancel/{id}', [CirculationController::class, 'cancelBorrow'])->name('circulation.cancel');

// Absen / Kunjungan
Route::get('/absen', [AbsenController::class, 'index'])->name('absen.index');

// Logout
Route::post('/logout', function () {
    // Logika logout sementara / sederhana
    auth()->logout();
    return redirect('/katalog-buku'); // atau redirect ke halaman login
})->name('logout');