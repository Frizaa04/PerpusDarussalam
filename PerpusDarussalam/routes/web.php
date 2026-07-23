<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController; 
use App\Http\Controllers\AbsenController; 
use App\Http\Controllers\LaporanController; 
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

// Halaman Awal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ==========================================
// AUTHENTICATION USER (Pemustaka)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/user/login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
    Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.post');
});
Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');


// ==========================================
// AUTHENTICATION ADMIN
// ==========================================
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Absen / Kunjungan
Route::get('/absen', [AbsenController::class, 'index'])->name('absen.index');
Route::post('/absen', [AbsenController::class, 'store'])->name('absen.store'); // <-- TAMBAHAN INI AGAR TIDAK ERROR

// ==========================================
// ROUTE DASHBOARD ADMIN (Diberi Middleware Admin)
// ==========================================
Route::middleware(['admin'])->group(function () {

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

    // Laporan Utama & Laporan Detail Koleksi
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/koleksi', [LaporanController::class, 'koleksi'])->name('laporan.koleksi');
    Route::get('/laporan/koleksi/export', [LaporanController::class, 'exportExcel'])->name('laporan.koleksi.export');

    // Logout Admin Legacy (bisa pakai /admin/logout)
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});