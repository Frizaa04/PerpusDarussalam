<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController; 
use App\Http\Controllers\AbsenController; 
use App\Http\Controllers\LaporanController; 
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
    Route::get('/user/home', [UserController::class, 'index'])->name('user.home');

    // 2. Area Anggota User
    Route::get('/user/area-anggota', [AreaAnggotaUserController::class, 'index'])->name('user.area_anggota');

    // 3. Update Password User (Area Anggota)
    Route::put('/user/area-anggota/update-password', [AreaAnggotaUserController::class, 'updatePassword'])->name('password.update.custom');

    // 3. E-Book User
    Route::get('/user/e-book', [UserEbookController::class, 'index'])->name('user.ebook.index');
    Route::get('/user/e-book/read/{id}', [UserEbookController::class, 'read'])->name('user.ebook.read');

    // Route menuju halaman detail buku / e-book user
    Route::get('/user/book/view/{id}', [UserController::class, 'showBookDetail'])->name('user.book.show');

    // 4. Route Tentang Kami
    Route::get('/user/tentang-kami', [AboutUserController::class, 'index'])->name('user.about');

    // 5. Simpan Push Notification Token User
    Route::post('/user/push-notification/subscribe', [PushNotificationController::class, 'store'])->name('push.subscribe');
    Route::get('/user/push-notification/test', [PushNotificationController::class, 'testNotification'])->name('push.test');

    // 6. Logout User
    Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');

});

// AUTHENTICATION ADMIN
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ROUTE DASHBOARD ADMIN 
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/announcement', [AdminAnnouncementController::class, 'store'])->name('admin.announcement.store');
    Route::patch('/admin/announcement/{id}/activate', [AdminAnnouncementController::class, 'activate'])->name('admin.announcement.activate');
    Route::delete('/admin/announcement/{id}', [AdminAnnouncementController::class, 'destroy'])->name('admin.announcement.destroy');

    Route::post('/admin/banner', [BannerController::class, 'store'])->name('admin.banner.store');
    Route::patch('/admin/banner/{id}/activate', [BannerController::class, 'activate'])->name('admin.banner.activate');
    Route::patch('/admin/banner/{id}/deactivate', [BannerController::class, 'deactivate'])->name('admin.banner.deactivate');
    Route::delete('/admin/banner/{id}', [BannerController::class, 'destroy'])->name('admin.banner.destroy');

    // Manajemen Siswa / User
    Route::get('/admin/manajemen-siswa', [MemberController::class, 'index'])->name('member.index');
    Route::post('/admin/manajemen-siswa/store', [MemberController::class, 'store'])->name('member.store');
    Route::put('/admin/manajemen-siswa/update/{id}', [MemberController::class, 'update'])->name('member.update');
    Route::delete('/admin/manajemen-siswa/destroy-multiple', [MemberController::class, 'destroyMultiple'])->name('member.destroyMultiple');
    Route::delete('/admin/manajemen-siswa/destroy-expired', [MemberController::class, 'destroyExpired'])->name('member.destroyExpired');
    Route::post('/admin/manajemen-siswa/cetak-kartu-batch', [MemberController::class, 'printCards'])->name('member.printCards');
    Route::put('/admin/manajemen-siswa/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
    Route::delete('/admin/manajemen-siswa/{id}', [MemberController::class, 'destroy'])->name('member.destroy');

    // Katalog Buku
    Route::get('/admin/katalog-buku', [BookController::class, 'index'])->name('book.index');
    Route::post('/admin/katalog-buku/store', [BookController::class, 'store'])->name('book.store');
    Route::put('/admin/katalog-buku/update', [BookController::class, 'update'])->name('book.update');
    Route::post('/admin/katalog-buku/kategori/store', [BookController::class, 'storeCategory'])->name('book.category.store');
    Route::delete('/admin/katalog-buku/kategori/{id}', [BookController::class, 'destroyCategory'])->name('book.category.destroy');
    Route::post('/admin/katalog-buku/destroy-multiple', [BookController::class, 'destroyMultiple'])->name('book.destroyMultiple');

    // Edit perbuku
    Route::get('/admin/book/{id}/items', [BookItemController::class, 'getItems']);
    Route::post('/admin/book/item/store', [BookItemController::class, 'store'])->name('book.item.store');
    Route::put('/admin/book/item/{id}', [BookItemController::class, 'update'])->name('book.item.update');
    Route::delete('/admin/book/destroy-multiple', [BookController::class, 'destroyMultiple'])->name('book.destroyMultiple');
    Route::delete('/admin/book/item/{id}', [BookItemController::class, 'destroy'])->name('book.item.destroy');
    Route::get('/admin/book/{id}/print-all-barcodes', [BookItemController::class, 'printAllBarcodes'])->name('book.print-all-barcodes');
    Route::get('/admin/book/item/{id}/print-barcode', [BookItemController::class, 'printBarcode'])->name('book.item.print-barcode');
    Route::get('/admin/book/item/print-selected', [BookItemController::class, 'printSelectedBarcodes'])->name('book.item.print.selected.barcodes');

    // Sirkulasi
    Route::get('/admin/sirkulasi', [CirculationController::class, 'index'])->name('circulation.index');
    Route::post('/admin/sirkulasi', [CirculationController::class, 'store'])->name('circulation.store');
    Route::post('/admin/sirkulasi/return/{id}', [CirculationController::class, 'returnBook'])->name('circulation.return');
    Route::post('/admin/circulation/cancel/{id}', [CirculationController::class, 'cancelBorrow'])->name('circulation.cancel');
    Route::post('/admin/sirkulasi/{id}/hilang', [CirculationController::class, 'loseBook'])->name('circulation.lose');

    // API Cek Anggota Otomatis
    Route::get('/admin/api/check-member/{nomor}', [CirculationController::class, 'getUserByNikNisn']);
    Route::get('/admin/api/check-book/{nomor}', [CirculationController::class, 'getBookByInventory']);

    // Halaman Transaksi Keuangan
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('transaction.index');
    Route::post('/admin/transaksi/store', [TransaksiController::class, 'store'])->name('transaction.store');
    Route::get('/admin/transaksi/{id}/edit', [TransaksiController::class, 'edit'])->name('transaction.edit');
    Route::put('/admin/transaksi/{id}', [TransaksiController::class, 'update'])->name('transaction.update');
    Route::delete('/admin/transaksi/bulk-delete', [TransaksiController::class, 'bulkDestroy'])->name('transaction.destroy.bulk');
    Route::get('/admin/transaksi/cari-user/{identitas}', [TransaksiController::class, 'cariUser'])->name('transaction.cariUser');
    Route::get('/admin/transaksi/tarif/{jenis}', [TransaksiController::class, 'getTarif'])->name('transaction.tarif');

    // E-Book
    Route::get('/admin/e-book', [EbookController::class, 'index'])->name('admin.ebook.index');
    Route::post('/admin/e-book/store', [EbookController::class, 'store'])->name('admin.ebook.store');
    Route::put('/admin/e-book/update/{id}', [EbookController::class, 'update'])->name('admin.ebook.update');
    Route::delete('/admin/e-book/delete/{id}', [EbookController::class, 'destroy'])->name('admin.ebook.destroy');
    Route::post('/admin/e-book/bulk-delete', [EbookController::class, 'bulkDestroy'])->name('admin.ebook.destroy.bulk');
    Route::delete('/admin/e-book/destroy-multiple', [EbookController::class, 'destroyMultiple'])->name('admin.ebook.destroy-multiple');

    // Absen / Kunjungan (Public)
    Route::get('/admin/absen', [AbsenController::class, 'index'])->name('absen.index');
    Route::post('/admin/absen', [AbsenController::class, 'store'])->name('absen.store');

    // Laporan Utama & Detail Laporan
    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/admin/laporan/koleksi', [LaporanController::class, 'koleksi'])->name('laporan.koleksi');
    Route::get('/admin/laporan/anggota', [LaporanController::class, 'anggota'])->name('laporan.anggota');
    Route::get('/admin/laporan/pengunjung', [LaporanController::class, 'pengunjung'])->name('laporan.pengunjung');
    Route::get('/admin/laporan/peminjaman', [LaporanController::class, 'peminjaman'])->name('laporan.peminjaman');

    // Route Export Excel
    Route::get('/admin/laporan/koleksi/export', [LaporanController::class, 'exportExcel'])->name('laporan.koleksi.export');
    Route::get('/admin/laporan/pengunjung/export', [LaporanController::class, 'exportPengunjungExcel'])->name('laporan.pengunjung.export');
    Route::get('/admin/laporan/anggota/export', [LaporanController::class, 'exportAnggotaExcel'])->name('laporan.anggota.export');
    Route::get('/admin/laporan/peminjaman/export', [LaporanController::class, 'exportPeminjamanExcel'])->name('laporan.peminjaman.export');
    Route::get('/admin/laporan/absensi/export', [LaporanController::class, 'exportAttendanceExcel'])->name('laporan.absensi.export');
    Route::get('/admin/laporan/transaksi/export', [LaporanController::class, 'exportKeuanganExcel'])->name('laporan.transaksi.export');
    
    // Route Import Excel
    Route::post('/admin/laporan/anggota/import', [LaporanController::class, 'importAnggota'])->name('laporan.anggota.import');
    Route::post('/admin/laporan/koleksi/import', [LaporanController::class, 'importKoleksi'])->name('laporan.koleksi.import');

    // Route Notifikasi
    Route::get('/admin/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/admin/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/admin/notifikasi/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.readAll');
    Route::post('/admin/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

});