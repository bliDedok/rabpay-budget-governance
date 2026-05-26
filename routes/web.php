<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Models\Vendor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/audit-trail', [MonitoringController::class, 'auditTrail'])->name('monitoring.audit');
    Route::get('/risk-monitoring', [MonitoringController::class, 'riskMonitoring'])->name('monitoring.risk');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/simulasi-transaksi', function () {
    $vendors = Vendor::where('status', 'active')->get();

    return view('transactions.simulation', compact('vendors'));
})->middleware(['auth'])->name('transactions.simulation');

Route::get('/vendor-qr', function () {
    $vendors = \App\Models\Vendor::where('status', 'active')->get();

    return view('vendors.qr', compact('vendors'));
})->middleware('auth')->name('vendors.qr');

require __DIR__.'/auth.php';
