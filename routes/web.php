<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RenunganController;
use App\Http\Controllers\Admin\WartaController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\ValidasiRuangController;
use App\Http\Controllers\Admin\PelayananController;
use App\Http\Controllers\User\KonfirmasiJadwalController;
use App\Services\ScheduleService;
use App\Http\Controllers\User\JadwalPelayananController;
use App\Http\Controllers\User\PinjamRuanganController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('landingpage/landingpage');
});
Route::get('/', [LandingPageController::class, 'showLandingPage']);

Route::get('/warta/{fileName}', function ($fileName) {
    // Validasi agar hanya PDF dan tidak mengakses file selain di folder warta
    if (!preg_match('/^[a-zA-Z0-9_\-]+\.(pdf)$/', $fileName)) {
        abort(403);
    }

    $path = storage_path("app/public/warta/{$fileName}");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});



Route::get('/renungan', [RenunganController::class, 'showRenungan']);
Route::get('/renungan/detail/{id}', [RenunganController::class, 'detailRenungan']);

Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{id}', [BeritaController::class, 'show'])->name('berita.detail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// User Routes
Route::middleware(['auth', 'userMiddleware'])->group(function () {

    Route::get('dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/user/jadwal-ruangan', [PinjamRuanganController::class, 'list'])->name('jadwal-ruangan');

    Route::get('/bookings/create', [PinjamRuanganController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [PinjamRuanganController::class, 'store'])->name('bookings.store');

    Route::get('/user/jadwal-pelayanan', [JadwalPelayananController::class, 'index'])->name('user.jadwal-pelayanan'); //Availabilities
    Route::post('/user/jadwal-pelayanan', [JadwalPelayananController::class, 'store'])->name('user.store.jadwal-pelayanan'); //Availabilities

    Route::post('/jadwal/{id}/confirm', [KonfirmasiJadwalController::class, 'confirmSchedule'])->name('user.confirm-schedule');
    Route::post('/jadwal/{id}/reject', [KonfirmasiJadwalController::class, 'rejectSchedule'])->name('user.reject-schedule');
});



// Admin Routes
Route::middleware(['auth', 'adminMiddleware'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/warta', [WartaController::class, 'index'])->name('admin.warta.index');
    Route::get('/admin/warta/upload-warta', [WartaController::class, 'create'])->name('admin.warta.create');
    Route::post('/admin/warta/upload-warta', [WartaController::class, 'uploadWarta'])->name('admin.warta.upload-warta');
    Route::delete('/admin/warta/destroy/{id}', [WartaController::class, 'destroy'])->name('admin.warta.destroy');

    Route::get('/admin/galeri', [GalleryController::class, 'index'])->name('admin.galeri');
    Route::post('/admin/galeri/upload', [GalleryController::class, 'uploadFoto'])->name('admin.galeri.upload');
    Route::delete('/admin/galeri/delete/{id}', [GalleryController::class, 'delete'])->name('admin.galeri.delete');

    Route::get('/admin/kelolaakun', [AdminController::class, 'list']);
    Route::get('/admin/add', [AdminController::class, 'add']);
    Route::post('/admin/add', [AdminController::class, 'insert']);
    Route::get('admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/edit/{id}', [AdminController::class, 'update']);
    Route::delete('admin/delete/{id}', [AdminController::class, 'delete']);

    Route::get('/admin/renungan/list', [RenunganController::class, 'listRenungan']);
    Route::get('/admin/renungan/tambah', [RenunganController::class, 'tambahRenungan']);
    Route::post('/admin/renungan/tambah', [RenunganController::class, 'insertRenungan']);
    Route::get('/admin/renungan/detail/{id}', [RenunganController::class, 'lihatDetail']);
    Route::get('/admin/renungan/edit/{id}', [RenunganController::class, 'editRenungan'])->name('renungan.edit');
    Route::post('/admin/renungan/edit/{id}', [RenunganController::class, 'updateRenungan']);
    Route::delete('/admin/renungan/delete/{id}', [RenunganController::class, 'deleteRenungan']);

    Route::get('/admin/berita', [BeritaController::class, 'listBerita']);
    Route::get('/admin/berita/tambah', [BeritaController::class, 'tambahBerita']);
    Route::post('/admin/berita/tambah', [BeritaController::class, 'insertBerita']);
    Route::get('/admin/berita/detail/{id}', [BeritaController::class, 'detailBerita']);
    Route::get('/admin/berita/edit/{id}', [BeritaController::class, 'editBerita']);
    Route::post('/admin/berita/edit/{id}', [BeritaController::class, 'updateBerita']);
    Route::delete('/admin/berita/delete/{id}', [BeritaController::class, 'deleteBerita']);


    // Route Kelola Jadwal Ruangan

    Route::get('/admin/ruangan/jadwal', [JadwalController::class, 'index']);
    Route::get('/api/events', [JadwalController::class, 'getEvents']);
    Route::delete('/admin/ruangan/delete/{id}', [JadwalController::class, 'deleteEvent']);
    Route::put('/admin/ruangan/update/{id}', [JadwalController::class, 'update']);
    Route::get('/admin/ruangan/search', [JadwalController::class, 'search']);
    Route::post('/create-schedule', [JadwalController::class, 'create'])->name('jadwal.create');

    Route::get('/admin/laporan-ruangan', [JadwalController::class, 'laporanRuangan'])->name('admin.laporan-ruangan');
    Route::get('/admin/laporan-ruangan/export-pdf', [JadwalController::class, 'exportPdf'])->name('admin.laporan-ruangan.export-pdf');

    // Route Validasi Peminjaman Ruangan
    Route::get('/admin/pinjam-ruangan', [ValidasiRuangController::class, 'index'])->name('admin.bookings.index');
    Route::post('/admin/pinjam-ruangan/{booking}/status', [ValidasiRuangController::class, 'updateStatus'])->name('admin.bookings.updateStatus');

    // Route Generate Jadwal Pelayanan
    Route::get('/admin/jadwal-pelayanan', [PelayananController::class, 'index'])->name('admin.jadwal-pelayanan');
    Route::get('/admin/generate-schedule', [PelayananController::class, 'generateSchedule'])->name('admin.generate-schedule');
    Route::get('/admin/jadwal/{id}/edit', [PelayananController::class, 'edit'])->name('admin.edit-jadwal');
    Route::put('/admin/jadwal/{id}', [PelayananController::class, 'update'])->name('admin.update-jadwal');  // Menggunakan PUT untuk update
    Route::delete('/admin/jadwal/{id}', [PelayananController::class, 'destroy'])->name('admin.delete-jadwal');


    Route::get('/admin/check-schedule-current-month', [ScheduleService::class, 'checkScheduleForCurrentMonth'])->name('admin.check-schedule-current-month');
    Route::get('/admin/generate-next-month-schedule', [ScheduleService::class, 'generateNextMonthSchedule'])->name('admin.generate-next-month-schedule');


    Route::get('/admin/laporan-pelayanan', [KonfirmasiJadwalController::class, 'getLaporan'])->name('laporan.pelayanan');
    Route::get('/admin/laporan-pelayanan/export-pdf', [KonfirmasiJadwalController::class, 'exportPDF'])->name('laporan.exportPDF');

    Route::get('/admin/check-next-month-schedule', [PelayananController::class, 'checkNextMonthSchedule'])->name('admin.check-next-month-schedule');
    Route::get('/admin/show-next-month-schedule', [PelayananController::class, 'showNextMonthSchedule'])->name('admin.show-next-month-schedule');
});
