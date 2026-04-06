<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'viewSelf'])->name('profile');
    Route::get('/profile/{id}', [ProfileController::class, 'view'])->name('profile.other');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'list'])->name('tickets.list');
    Route::get('/tickets/create', [App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/store', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}/view', [App\Http\Controllers\TicketController::class, 'view'])->name('tickets.view');
    Route::get('/tickets/{id}/edit', [App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
    Route::get('/tickets/{id}/log', [App\Http\Controllers\TicketController::class, 'log'])->name('tickets.log');
    Route::post('/tickets/{id}/log/store', [App\Http\Controllers\TicketController::class, 'storeLog'])->name('tickets.log.store');
    Route::post('/tickets/{id}/client-validation', [App\Http\Controllers\TicketController::class, 'clientValidation'])->name('tickets.client.validation');
    Route::put('/tickets/{id}/update', [App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/delete', [App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'list'])->name('projects.list');
    Route::get('/projects/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects/store', [App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/view', [App\Http\Controllers\ProjectController::class, 'view'])->name('projects.view');
    Route::get('/projects/{id}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}/update', [App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/delete', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'settings'])->name('settings');

require __DIR__.'/auth.php';
