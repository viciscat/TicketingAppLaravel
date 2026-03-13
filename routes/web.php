<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'list'])->name('tickets.list');
Route::get('/tickets/create', [App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
Route::get('/tickets/view', [App\Http\Controllers\TicketController::class, 'view'])->name('tickets.view');

Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'list'])->name('projects.list');
Route::get('/projects/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
Route::get('/projects/view', [App\Http\Controllers\ProjectController::class, 'view'])->name('projects.view');

Route::get('/login', [App\Http\Controllers\AccountController::class, 'login'])->name('login');
Route::get('/login/forgot', [App\Http\Controllers\AccountController::class, 'forgotPassword'])->name('login.forgot');
Route::get('/register', [App\Http\Controllers\AccountController::class, 'register'])->name('register');
Route::get('/profile', [App\Http\Controllers\AccountController::class, 'viewProfile'])->name('profile');

Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'settings'])->name('settings');
