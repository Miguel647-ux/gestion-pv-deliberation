<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'da'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('years', AcademicYearController::class);
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('logs',  [AuditLogController::class, 'index'])->name('logs.index');
});