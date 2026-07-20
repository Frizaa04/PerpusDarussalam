<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController; 

Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/manajemen-siswa', [MemberController::class, 'index'])->name('member.index');
Route::put('/manajemen-siswa/update', [MemberController::class, 'update'])->name('member.update');

Route::get('/katalog-buku', [BookController::class, 'index'])->name('book.index');
Route::get('/sirkulasi', [CirculationController::class, 'index'])->name('circulation.index');