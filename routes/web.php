<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BulkRequestController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/bulk-requests', [BulkRequestController::class, 'index'])->name('bulk-requests.index');
    Route::post('/bulk-requests', [BulkRequestController::class, 'store'])->name('bulk-requests.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
