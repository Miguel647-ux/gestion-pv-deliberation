<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'administration — propriétaire : P1
|--------------------------------------------------------------------------
|
| À remplir au pas 10 :
|   - CRUD /admin/years       → admin.years.*
|   - GET  /admin/users       → admin.users.index   (lecture seule)
|   - GET  /admin/logs        → admin.logs.index    (lecture seule)
|
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // P1 remplira ici.
});