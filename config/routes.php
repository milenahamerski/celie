<?php

use Core\Router\Route;
use App\Controllers\AuthenticationsController;
use App\Controllers\ContactsController;
use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\MemberController;
use App\Middleware\Authenticate;

Route::get('/', [HomeController::class, 'index'])->name('root');

Route::get('/login', [AuthenticationsController::class, 'new'])->name('users.login');
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('users.authenticate');
Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('users.logout');
    Route::get('/home', [HomeController::class, 'index'])->name('users.home');
});

Route::middleware('auth.admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});

Route::middleware('auth.member')->group(function () {
    Route::get('/member', [MemberController::class, 'index'])->name('member.index');
    Route::get('/contacts/new', [ContactsController::class, 'new'])->name('contacts.new');
    Route::post('/contacts', [ContactsController::class, 'create'])->name('contacts.create');
    Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
    Route::delete('/contacts/{id}', [ContactsController::class, 'destroy'])->name('contacts.delete');
});
