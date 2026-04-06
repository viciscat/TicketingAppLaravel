<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/projects/members', [ProjectController::class, 'apiAddMember'])->name('api.projects.members.store');
    Route::delete('/projects/members', [ProjectController::class, 'apiRemoveMember'])->name('api.projects.members.destroy');
    Route::get('/users/suggestions', [UserController::class, 'apiGetSuggestions'])->name('api.users.suggestions');

    Route::get('/tickets/list', [TicketController::class, 'listApi'])->name('api.tickets.list');
    Route::post('/tickets/members', [TicketController::class, 'apiAssignTo'])->name('api.tickets.assigned.store');
});


