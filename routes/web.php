<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Informasi Akuntansi Agen BRILink Rambo
|--------------------------------------------------------------------------
*/

// ── Root: redirect ke dashboard jika sudah login, ke login jika belum ──
Route::get('/', fn () => redirect()->route('dashboard'));

// ── Route grup protected: wajib auth + role owner ──────────────────────
Route::middleware(['auth', 'verified', 'owner'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Manajemen Transaksi (resource: index, create, store, show, edit, update, destroy)
    Route::resource('transaksi', TransactionController::class);

    // Laporan & Export
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/{format}', [ReportController::class, 'export'])
        ->name('laporan.export')
        ->where('format', 'pdf|excel');

    // Kelola User
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Audit Trail
    Route::get('/audit-log', [ActivityLogController::class, 'index'])->name('audit-log.index');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Auth routes (login, logout, register, dll.) dari Breeze ────────────
require __DIR__.'/auth.php';
