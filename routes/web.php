<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [\App\Http\Controllers\Api\DashboardStatsController::class, 'index'])->name('api.dashboard.stats');
    Route::get('/api/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications');
    Route::get('/api/monitoring/updates', [\App\Http\Controllers\Api\MonitoringApiController::class, 'latest'])->name('api.monitoring.updates');
    Route::post('/pajak/sync', [PajakController::class, 'sync'])->name('pajak.sync');
    
    Route::get('/pajak/export', [PajakController::class, 'export'])->name('pajak.export');
    Route::resource('pajak', PajakController::class);
    Route::resource('monitoring', MonitoringController::class)->only(['index', 'store', 'destroy']);
    Route::resource('dokumen', DokumenController::class)->only(['index', 'store', 'destroy'])->parameters(['dokumen' => 'dokumen']);
    
    Route::resource('user', UserController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
});
