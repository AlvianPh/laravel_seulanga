<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Struktur middleware:
|   - 'auth'  : wajib login (Breeze default)
|   - 'owner' : hanya Role Owner (EnsureOwner middleware)
|
| Modul operasional (dashboard, kamar, penghuni, kontrak, tagihan,
| pembayaran, pengeluaran) bisa diakses Owner MAUPUN Admin.
|
| Manajemen User (CRUD akun) HANYA bisa diakses Owner.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ── Modul yang bisa diakses Owner DAN Admin ──────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile (bawaan Breeze — edit profil diri sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul Kamar
    Route::resource('rooms', \App\Http\Controllers\RoomController::class);
    Route::delete('/rooms/{room}/photos/{photo}', [\App\Http\Controllers\RoomController::class, 'deletePhoto'])->name('rooms.photos.destroy');
    Route::patch('/rooms/{room}/photos/{photo}/primary', [\App\Http\Controllers\RoomController::class, 'setPrimaryPhoto'])->name('rooms.photos.primary');

    // Modul Penghuni
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::delete('/tenants/{tenant}/ktp', [\App\Http\Controllers\TenantController::class, 'deleteKtp'])->name('tenants.ktp.destroy');
    Route::delete('/tenants/{tenant}/photo', [\App\Http\Controllers\TenantController::class, 'deletePhoto'])->name('tenants.photo.destroy');

    // Modul Kontrak
    Route::resource('contracts', \App\Http\Controllers\ContractController::class);
    Route::post('/contracts/{contract}/renew', [\App\Http\Controllers\ContractController::class, 'renew'])->name('contracts.renew');
    Route::post('/contracts/{contract}/terminate', [\App\Http\Controllers\ContractController::class, 'terminate'])->name('contracts.terminate');

    // Modul Tagihan
    Route::post('/invoices/generate-manual', [\App\Http\Controllers\InvoiceController::class, 'generateManual'])->name('invoices.generate-manual');
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class)->except(['create', 'store']);

    /*
    |------------------------------------------------------------------
    | Placeholder untuk modul operasional (akan diisi di Tahap 4+):
    |   - /pembayaran     (PaymentController)
    |   - /pengeluaran    (ExpenseController)
    |------------------------------------------------------------------
    */
});

// ── Manajemen User — HANYA Owner ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'owner'])->group(function () {

    // CRUD akun user (Resource Controller)
    Route::resource('users', UserController::class);

    /*
    |------------------------------------------------------------------
    | Placeholder untuk modul Owner-only (akan diisi di Tahap 4+):
    |   - /laporan        (ReportController)
    |------------------------------------------------------------------
    */
});

require __DIR__.'/auth.php';
