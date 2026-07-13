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

    /*
    |------------------------------------------------------------------
    | Placeholder untuk modul operasional (akan diisi di Tahap 4+):
    |   - /kamar          (RoomController)
    |   - /penghuni       (TenantController)
    |   - /kontrak        (ContractController)
    |   - /tagihan        (InvoiceController)
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
