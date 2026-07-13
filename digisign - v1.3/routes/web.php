<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page publik. Kalau sudah login, langsung ke dashboard.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing.index');
})->name('landing');

// Route auth (login, register, logout) — dari Laravel Breeze
require __DIR__ . '/auth.php';

// ===== SEMUA ROUTE DI BAWAH INI WAJIB LOGIN =====
Route::middleware('auth')->group(function () {

    // ----- Dashboard -----
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // ----- Documents -----
    Route::get('/documents',              [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create',       [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents',             [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}',   [DocumentController::class, 'show'])->name('documents.show');
    Route::delete('/documents/{document}',[DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
    ->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // ----- Signatures -----
    Route::get('/pending', [SignatureController::class, 'pending'])->name('signatures.pending');
    Route::get('/documents/{document}/sign',  [SignatureController::class, 'show'])->name('signatures.show');
    Route::post('/documents/{document}/sign', [SignatureController::class, 'store'])->name('signatures.store');
    Route::get('/signatures/{signature}/image', [SignatureController::class, 'image'])->name('signatures.image');

    // ----- Verify -----
    Route::get('/verify',  [VerifyController::class, 'index'])->name('verify.index');
    Route::post('/verify', [VerifyController::class, 'verify'])->name('verify.check');

    // ----- Audit Log (milik sendiri) -----
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

    // ===== ADMIN ONLY =====
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',            [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit',[UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',     [UserController::class, 'update'])->name('users.update');

        Route::get('/audit-logs', [AuditLogController::class, 'adminIndex'])->name('audit.index');
    });
});
