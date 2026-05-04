<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api-docs', function () {
    return view('api-docs');
});

// CSV Export routes (protected by auth + admin)
Route::middleware(['auth'])->prefix('admin-export')->group(function () {
    Route::get('/users', [ExportController::class, 'users'])->name('export.users');
    Route::get('/needs', [ExportController::class, 'needs'])->name('export.needs');
    Route::get('/audit-logs', [ExportController::class, 'auditLogs'])->name('export.audit-logs');
});
