<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController; 
use App\Http\Controllers\AbsenController; 
use App\Http\Controllers\LaporanController; 
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\EbookController; 
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BookItemController;
use App\Http\Controllers\AreaAnggotaUserController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\UserEbookController;
use App\Http\Controllers\AboutUserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\PushNotificationController; 

// Halaman Awal (Public) - Langsung Mengarah ke Home User
Route::get('/', function () {
    return redirect()->route('user.home');
})->name('welcome');

// AUTHENTICATION USER (Pemustaka)
Route::middleware('guest')->group(function () {
    // Diberi name('login') agar middleware auth otomatis mengarah ke sini saat user belum login
    Route::get('/user/login', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.post');
});

Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');

Route::middleware(['auth:web'])->group(function () {
    
    // 1. Beranda User (Ganti closure function menjadi memanggil UserController)
    Route::get('/home', [UserController::class, 'index'])->name('user.home');

    // 2. Area Anggota User
    Route::get('/area-anggota', [AreaAnggotaUserController::class, 'index'])->name('user.area_anggota');

    // 3. Update Password User (Area Anggota)
    Route::put('/area-anggota/update-password', [AreaAnggotaUserController::class, 'updatePassword'])->name('password.update.custom');

    // 3. E-Book User
    Route::get('/user/e-book', [UserEbookController::class, 'index'])->name('user.ebook.index');
    Route::get('/user/e-book/read/{id}', [UserEbookController::class, 'read'])->name('user.ebook.read');

    // Route menuju halaman detail buku / e-book user
    Route::get('/book/view/{id}', [UserController::class, 'showBookDetail'])->name('user.book.show');

    // 4. Route Tentang Kami
    Route::get('/tentang-kami', [AboutUserController::class, 'index'])->name('user.about');

    // 5. Simpan Push Notification Token User
    Route::post('/push-notification/subscribe', [PushNotificationController::class, 'store'])->name('push.subscribe');
    Route::get('/push-notification/test', [PushNotificationController::class, 'testNotification'])->name('push.test');

    // 6. Logout User
    Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');

});

// AUTHENTICATION ADMIN
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Absen / Kunjungan (Public)
Route::get('/absen', [AbsenController::class, 'index'])->name('absen.index');
Route::post('/absen', [AbsenController::class, 'store'])->name('absen.store');

// ROUTE DASHBOARD ADMIN 
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/announcement', [AdminAnnouncementController::class, 'store'])->name('admin.announcement.store');
    Route::patch('/admin/announcement/{id}/activate', [AdminAnnouncementController::class, 'activate'])->name('admin.announcement.activate');
    Route::delete('/admin/announcement/{id}', [AdminAnnouncementController::class, 'destroy'])->name('admin.announcement.destroy');

    Route::post('/banner', [BannerController::class, 'store'])->name('admin.banner.store');
    Route::patch('/banner/{id}/activate', [BannerController::class, 'activate'])->name('admin.banner.activate');
    Route::patch('/banner/{id}/deactivate', [BannerController::class, 'deactivate'])->name('admin.banner.deactivate');
    Route::delete('/banner/{id}', [BannerController::class, 'destroy'])->name('admin.banner.destroy');

    // Manajemen Siswa / User
    Route::get('/manajemen-siswa', [MemberController::class, 'index'])->name('member.index');
    Route::post('/manajemen-siswa/store', [MemberController::class, 'store'])->name('member.store');
    Route::put('/manajemen-siswa/update/{id}', [MemberController::class, 'update'])->name('member.update');
    Route::delete('/manajemen-siswa/destroy-multiple', [MemberController::class, 'destroyMultiple'])->name('member.destroyMultiple');
    Route::delete('/manajemen-siswa/destroy-expired', [MemberController::class, 'destroyExpired'])->name('member.destroyExpired');
    Route::post('/manajemen-siswa/cetak-kartu-batch', [MemberController::class, 'printCards'])->name('member.printCards');
    Route::put('/manajemen-siswa/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
    Route::delete('/manajemen-siswa/{id}', [MemberController::class, 'destroy'])->name('member.destroy');

    // Katalog Buku
    Route::get('/katalog-buku', [BookController::class, 'index'])->name('book.index');
    Route::post('/katalog-buku/store', [BookController::class, 'store'])->name('book.store');
    Route::put('/katalog-buku/update', [BookController::class, 'update'])->name('book.update');

    // Edit perbuku
    Route::get('/book/{id}/items', [BookItemController::class, 'getItems']);
    Route::post('/book/item/store', [BookItemController::class, 'store'])->name('book.item.store');
    Route::put('/book/item/{id}', [BookItemController::class, 'update'])->name('book.item.update');
    Route::delete('/book/destroy-multiple', [BookController::class, 'destroyMultiple'])->name('book.destroyMultiple');
    Route::delete('/book/item/{id}', [BookItemController::class, 'destroy'])->name('book.item.destroy');
    Route::get('/book/{id}/print-all-barcodes', [BookItemController::class, 'printAllBarcodes'])->name('book.print-all-barcodes');
    Route::get('/book/item/{id}/print-barcode', [BookItemController::class, 'printBarcode'])->name('book.item.print-barcode');
    Route::get('/book/item/print-selected', [BookItemController::class, 'printSelectedBarcodes'])->name('book.item.print.selected.barcodes');

    // Sirkulasi
    Route::get('/sirkulasi', [CirculationController::class, 'index'])->name('circulation.index');
    Route::post('/sirkulasi', [CirculationController::class, 'store'])->name('circulation.store');
    Route::post('/sirkulasi/return/{id}', [CirculationController::class, 'returnBook'])->name('circulation.return');
    Route::post('/circulation/cancel/{id}', [CirculationController::class, 'cancelBorrow'])->name('circulation.cancel');

    // API Cek Anggota Otomatis
    Route::get('/api/check-member/{nomor}', [CirculationController::class, 'getUserByNikNisn']);
    Route::get('/api/check-book/{nomor}', [CirculationController::class, 'getBookByInventory']);

    // Halaman Transaksi Keuangan
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaction.index');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaction.store');
    Route::get('/transaksi/{id}/edit', [TransaksiController::class, 'edit'])->name('transaction.edit');
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update'])->name('transaction.update');
    Route::delete('/transaksi/bulk-delete', [TransaksiController::class, 'bulkDestroy'])->name('transaction.destroy.bulk');
    Route::get('/transaksi/cari-user/{identitas}', [TransaksiController::class, 'cariUser'])->name('transaction.cariUser');

    // E-Book
    Route::get('/e-book', [EbookController::class, 'index'])->name('admin.ebook.index');
    Route::post('/e-book/store', [EbookController::class, 'store'])->name('admin.ebook.store');
    Route::put('/e-book/update/{id}', [EbookController::class, 'update'])->name('admin.ebook.update');
    Route::delete('/e-book/delete/{id}', [EbookController::class, 'destroy'])->name('admin.ebook.destroy');
    Route::post('/e-book/bulk-delete', [EbookController::class, 'bulkDestroy'])->name('admin.ebook.destroy.bulk');
    Route::delete('/e-book/destroy-multiple', [EbookController::class, 'destroyMultiple'])->name('admin.ebook.destroy-multiple');

    // Laporan Utama & Detail Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/koleksi', [LaporanController::class, 'koleksi'])->name('laporan.koleksi');
    Route::get('/laporan/anggota', [LaporanController::class, 'anggota'])->name('laporan.anggota');
    Route::get('/laporan/pengunjung', [LaporanController::class, 'pengunjung'])->name('laporan.pengunjung');
    Route::get('/laporan/peminjaman', [LaporanController::class, 'peminjaman'])->name('laporan.peminjaman');
    
    // Laporan Keuangan
    Route::get('/laporan-keuangan', [LaporanKeuanganController::class, 'index'])->name('laporan.keuangan');

    // Route Export Excel
    Route::get('/laporan/koleksi/export', [LaporanController::class, 'exportExcel'])->name('laporan.koleksi.export');
    Route::get('/laporan/pengunjung/export', [LaporanController::class, 'exportPengunjungExcel'])->name('laporan.pengunjung.export');
    Route::get('/laporan/anggota/export', [LaporanController::class, 'exportAnggotaExcel'])->name('laporan.anggota.export');
    Route::get('/laporan/peminjaman/export', [LaporanController::class, 'exportPeminjamanExcel'])->name('laporan.peminjaman.export');
    Route::get('/laporan/absensi/export', [LaporanController::class, 'exportAttendanceExcel'])->name('laporan.absensi.export');
    Route::get('/laporan/transaksi/export', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan.transaksi.export');
    
    // Route Import Excel
    Route::post('/laporan/anggota/import', [LaporanController::class, 'importAnggota'])->name('laporan.anggota.import');
    Route::post('/laporan/koleksi/import', [LaporanController::class, 'importKoleksi'])->name('laporan.koleksi.import');

    // Route Notifikasi
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.readAll');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

});