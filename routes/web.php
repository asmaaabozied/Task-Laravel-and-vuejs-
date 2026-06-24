<?php

use App\Http\Controllers\TicketEscalationController;
use App\Http\Controllers\TicketPageController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('tickets/{ticket}/escalate', [TicketPageController::class, 'show'])
    ->name('tickets.escalate');

Route::prefix('api')->group(function () {
    Route::post('tickets/{ticket}/escalate', TicketEscalationController::class);
});

// Include health check routes
require __DIR__.'/health.php';
