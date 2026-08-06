<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| HALAMAN AWAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ROUTE YANG DAPAT DIAKSES PENGGUNA SETELAH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DATA SPAREPART
    |--------------------------------------------------------------------------
    */

    Route::get('/sparepart', [SparepartController::class, 'index'])
        ->name('sparepart.index');

    Route::get('/sparepart/riwayat', [SparepartController::class, 'riwayat'])
        ->name('sparepart.riwayat');

    /*
    |--------------------------------------------------------------------------
    | BARANG MASUK
    |--------------------------------------------------------------------------
    */

    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])
        ->name('barang-masuk.index');

    /*
    |--------------------------------------------------------------------------
    | BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])
        ->name('barang-keluar.index');

    /*
    |--------------------------------------------------------------------------
    | EXPORT DATA
    |--------------------------------------------------------------------------
    |
    | Semua export:
    | - Barang Masuk
    | - Barang Keluar
    | - Data Gabungan CSV
    |
    | diarahkan ke ExportController.
    |
    */

    Route::get('/export-data', [ExportController::class, 'export'])
        ->name('export.data');
});

/*
|--------------------------------------------------------------------------
| ROUTE KHUSUS ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | KELOLA SPAREPART
    |--------------------------------------------------------------------------
    */

    Route::post('/sparepart', [SparepartController::class, 'store'])
        ->name('sparepart.store');

    Route::put('/sparepart/{id}', [SparepartController::class, 'update'])
        ->name('sparepart.update');

    Route::delete('/sparepart/{sparepart}', [SparepartController::class, 'destroy'])
        ->name('sparepart.destroy');

    Route::put('/sparepart/{id}/activate', [SparepartController::class, 'activate'])
        ->name('sparepart.activate');

    /*
    |--------------------------------------------------------------------------
    | KELOLA BARANG MASUK
    |--------------------------------------------------------------------------
    */

    Route::post('/barang-masuk', [BarangMasukController::class, 'store'])
        ->name('barang-masuk.store');

    Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'destroy'])
        ->name('barang-masuk.destroy');

    /*
    |--------------------------------------------------------------------------
    | KELOLA BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])
        ->name('barang-keluar.store');

    Route::delete('/barang-keluar/{id}', [BarangKeluarController::class, 'destroy'])
        ->name('barang-keluar.destroy');
});

/*
|--------------------------------------------------------------------------
| ROUTE AUTENTIKASI
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';