<?php

use App\Controllers\AuthenticationsController;
use App\Controllers\HomeController;
use App\Controllers\AdminsController;
use App\Controllers\MembersController;

use Core\Router\Route;

Route::get('/', [HomeController::class, 'index'])->name('root');

Route::get('/login', [AuthenticationsController::class, 'showLogin'])->name('users.login');

Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');


Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');

    Route::middleware('member')->group(function () {
        Route::get('/member', [MembersController::class, 'index'])->name('member.index');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminsController::class, 'index'])->name('admin.index');
    });
});
