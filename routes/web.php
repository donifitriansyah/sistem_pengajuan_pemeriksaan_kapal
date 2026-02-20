<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApprovalUserController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Arsiparis\ArsiparisController;
use App\Http\Controllers\Arsiparis\DashboardController;
use App\Http\Controllers\Arsiparis\SuratKeluarController;
use App\Http\Controllers\Arsiparis\SuratMasukController;
use App\Http\Controllers\Petugas\DashboardPetugasController;
use App\Http\Controllers\Petugas\PenugasanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Jika sudah login
    if (auth()->check()) {
        $user = auth()->user();

        // Redirect sesuai role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'arsiparis_wilker':
                return redirect()->route('arsiparis.verifikasi');
            case 'kawilker':
                return redirect()->route('petugas.dashboard');
            case 'petugas-kapal':
                return redirect()->route('petugas-kapal.dashboard');
            case 'bendahara_wilker':
                return redirect()->route('petugas.pembayaran');
            default:
                return redirect()->route('user.dashboard');
        }
    }

    // Jika belum login, tampilkan view login
    return view('login');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('approval', ApprovalUserController::class);

    Route::get('/admin/approval', [ApprovalUserController::class, 'index'])
        ->name('approval.index');

    Route::put('/admin/user/{id}/approve',
        [ApprovalUserController::class, 'approve']
    )->name('user.approve');

    Route::get('admin/users', [UserManagementController::class, 'index'])
        ->name('users');

    Route::put('/users/{id}/reset-password',
        [UserManagementController::class, 'resetPassword']
    )->name('reset');

    Route::put('/users/{id}/reject', [UserManagementController::class, 'reject'])
        ->name('user.reject');

    Route::put('/users/{id}/reset-password',
        [UserManagementController::class, 'resetPassword']
    )->name('user.reset');

    Route::get('/user/{id}/edit', [UserManagementController::class, 'edit'])
        ->name('user.edit');

    Route::put('/user/{id}', [UserManagementController::class, 'update'])
        ->name('user.update');

    Route::delete('/user/{id}', [UserManagementController::class, 'destroy'])
        ->name('user.destroy');

});

Route::middleware(['auth', 'verified', 'user'])->group(function () {

    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('user.dashboard');

    Route::post('/pembayaran/{penagihan}', [UserDashboardController::class, 'store'])
        ->name('user.pembayaran.store');

    Route::post('/pengajuan/store',
        [PengajuanController::class, 'store']
    )->name('pengajuan.store');

    Route::put('/pengajuan/{id}', [UserDashboardController::class, 'update'])->name('user.pengajuan.update');
    Route::get('/export-excel', [UserDashboardController::class, 'exportExcel'])->name('export.excel');
});

Route::get('/kwitansi/{penagihan}', [UserDashboardController::class, 'show'])
    ->name('invoice.show');

Route::get('/invoice/{penagihan}', [UserDashboardController::class, 'showKwitansi'])
    ->name('kwitansi.show');

Route::get('/invoice/{penagihan}/download', [UserDashboardController::class, 'download'])
    ->name('invoice.download');

Route::get('/invoice/verify/{penagihan}', [UserDashboardController::class, 'verify'])
    ->name('invoice.verify');

// Cek status invoice berdasarkan kode bayar
Route::get('/cek-invoice/{kodeBayar}', [UserDashboardController::class, 'cekInvoice']);

Route::middleware(['auth', 'arsiparis'])->group(function () {

    Route::get('/dashboard/arsiparis', [DashboardController::class, 'index'])
        ->name('arsiparis.dashboard');

    Route::get('/dashboard/arsiparis/verifikasi', [DashboardController::class, 'indexStatus'])
        ->name('arsiparis.verifikasi');

    Route::get('/arsiparis/sudah-diagendakan', [DashboardController::class, 'indexSudahDiagendakan'])
        ->name('arsiparis.sudah-diagendakan');

    Route::get('/arsiparis/surat-masuk', [SuratMasukController::class, 'index'])
        ->name('arsiparis.surat-masuk');

    Route::get('/arsiparis/surat-keluar', [SuratKeluarController::class, 'index'])
        ->name('arsiparis.surat-keluar');

    Route::post('/arsiparis/arsipkan/{id}',
        [ArsiparisController::class, 'arsipkan'])
        ->name('arsiparis.arsipkan');

    Route::post('/pengajuan/status/{id}', [DashboardPetugasController::class, 'updateStatus'])->name('pengajuan.updateStatus');

});

Route::middleware(['auth', 'checkroles:kawilker|bendahara_wilker'])->group(function () {
    Route::put('/admin/pembayaran/{pembayaran}/verifikasi',
        [DashboardPetugasController::class, 'verifikasi']
    )->name('admin.pembayaran.verifikasi');
});

Route::middleware(['auth', 'petugas'])->group(function () {

    Route::get('/petugas/pembayaran', [DashboardPetugasController::class, 'indexPembayaranPetugas'])
        ->name('petugas.dashboard.petugas');

    Route::get('/dashboard/petugas', [DashboardPetugasController::class, 'index'])
        ->name('petugas.dashboard');

    Route::get('/pengajuan/petugas', [DashboardPetugasController::class, 'indexPengajuan'])
        ->name('pengajuan.petugas');

    Route::get('/pemeriksa/petugas', [DashboardPetugasController::class, 'indexPemeriksa'])
        ->name('petugas.pemeriksa');

    Route::get('/petugas/approval', [ApprovalUserController::class, 'indexPetugas'])
        ->name('petugas.approval.index');

    Route::put('/petugas/user/{id}/approve',
        [ApprovalUserController::class, 'approvePetugas']
    )->name('petugas.user.approve');

    Route::post(
        '/petugas/penagihan/{pengajuan}',
        [PenugasanController::class, 'store']
    )->name('petugas.penagihan.store');

    Route::get('admin/users', [UserManagementController::class, 'index'])
        ->name('users');

    Route::put('/users/{id}/reset-password',
        [UserManagementController::class, 'resetPassword']
    )->name('reset');

    Route::put('/users/{id}/reject', [UserManagementController::class, 'reject'])
        ->name('user.reject');

    Route::put('/users/{id}/reset-password',
        [UserManagementController::class, 'resetPassword']
    )->name('user.reset');

    Route::get('/user/{id}/edit', [UserManagementController::class, 'edit'])
        ->name('user.edit');

    Route::put('/user/{id}', [UserManagementController::class, 'update'])
        ->name('user.update');

    Route::delete('/user/{id}', [UserManagementController::class, 'destroy'])
        ->name('user.destroy');

    Route::post('/petugas/pengajuan/{id}', [DashboardPetugasController::class, 'update'])->name('pengajuan.update');

});

Route::middleware(['auth', 'keuangan'])->group(function () {

    Route::get('/dashboard/keuangan', [DashboardPetugasController::class, 'indexPembayaran'])
        ->name('petugas.pembayaran');

    Route::post(
        '/keuangan/penagihan/{pengajuan}',
        [PenugasanController::class, 'storeKeuangan']
    )->name('petugas.penagihan.store');

    Route::get('/keuangan/petugas', [DashboardPetugasController::class, 'indexKeuangan'])
        ->name('petugas.dashboard');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
