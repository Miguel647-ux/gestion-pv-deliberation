<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliberationPvController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('pvs')->name('pvs.')->group(function () {
        Route::get('/', [DeliberationPvController::class, 'index'])->name('index');
        Route::get('/create', [DeliberationPvController::class, 'create'])->name('create');
        Route::post('/', [DeliberationPvController::class, 'store'])->name('store');
        Route::get('/{pv}', [DeliberationPvController::class, 'show'])->name('show');
        Route::get('/{pv}/edit', [DeliberationPvController::class, 'edit'])->name('edit');
        Route::put('/{pv}', [DeliberationPvController::class, 'update'])->name('update');
        Route::post('/{pv}/archive', [DeliberationPvController::class, 'archive'])->name('archive');
        Route::delete('/{pv}', [DeliberationPvController::class, 'destroy'])->name('destroy');
        Route::get('/{pv}/file', [DeliberationPvController::class, 'viewFile'])->name('file');
    });
});