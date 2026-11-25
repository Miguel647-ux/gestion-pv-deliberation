<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliberationPvController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/pvs', [DeliberationPvController::class, 'index'])->name('pvs.index');
    Route::get('/pvs/create', [DeliberationPvController::class, 'create'])->name('pvs.create');
    Route::post('/pvs', [DeliberationPvController::class, 'store'])->name('pvs.store');
    Route::get('/pvs/{pv}', [DeliberationPvController::class, 'show'])->name('pvs.show');
    Route::get('/pvs/{pv}/edit', [DeliberationPvController::class, 'edit'])->name('pvs.edit');
    Route::put('/pvs/{pv}', [DeliberationPvController::class, 'update'])->name('pvs.update');
    Route::patch('/pvs/{pv}/archive', [DeliberationPvController::class, 'archive'])->name('pvs.archive');
    Route::delete('/pvs/{pv}', [DeliberationPvController::class, 'destroy'])->name('pvs.destroy');
    Route::get('/pvs/{pv}/file', [DeliberationPvController::class, 'viewFile'])->name('pvs.file');
});