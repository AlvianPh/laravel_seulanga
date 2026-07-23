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

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile (bawaan Breeze — edit profil diri sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul Kamar
    Route::resource('rooms', \App\Http\Controllers\RoomController::class)->withTrashed(['show']);
    Route::delete('/rooms/{room}/photos/{photo}', [\App\Http\Controllers\RoomController::class, 'deletePhoto'])->name('rooms.photos.destroy');
    Route::patch('/rooms/{room}/photos/{photo}/primary', [\App\Http\Controllers\RoomController::class, 'setPrimaryPhoto'])->name('rooms.photos.primary');

    // Master Data Referensi
    Route::resource('room_types', \App\Http\Controllers\RoomTypeController::class);
    Route::resource('facilities', \App\Http\Controllers\FacilityController::class);
    Route::resource('payment_methods', \App\Http\Controllers\PaymentMethodController::class)->except(['show']);
    Route::resource('expense_categories', \App\Http\Controllers\ExpenseCategoryController::class)->except(['show']);
    Route::resource('bank_accounts', \App\Http\Controllers\BankAccountController::class)->except(['show']);
    Route::resource('additional_fee_types', \App\Http\Controllers\AdditionalFeeTypeController::class)->except(['show']);

    // Modul Penghuni
    Route::resource('tenants', \App\Http\Controllers\TenantController::class)->withTrashed(['show']);
    Route::delete('/tenants/{tenant}/ktp', [\App\Http\Controllers\TenantController::class, 'deleteKtp'])->name('tenants.ktp.destroy');
    Route::delete('/tenants/{tenant}/photo', [\App\Http\Controllers\TenantController::class, 'deletePhoto'])->name('tenants.photo.destroy');

    // Modul Kontrak
    Route::resource('contracts', \App\Http\Controllers\ContractController::class);
    Route::post('/contracts/{contract}/renew', [\App\Http\Controllers\ContractController::class, 'renew'])->name('contracts.renew');
    Route::post('/contracts/{contract}/terminate', [\App\Http\Controllers\ContractController::class, 'terminate'])->name('contracts.terminate');

    // Modul Tagihan
    Route::post('/invoices/generate-manual', [\App\Http\Controllers\InvoiceController::class, 'generateManual'])->name('invoices.generate-manual');
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class)->except(['create', 'store']);

    // Modul Pembayaran
    Route::get('/payments/{payment}/verify', [\App\Http\Controllers\PaymentController::class, 'verifyForm'])->name('payments.verify');
    Route::post('/payments/{payment}/verify', [\App\Http\Controllers\PaymentController::class, 'processVerification'])->name('payments.process-verification');
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->except(['edit', 'update', 'destroy']);

    // Modul Pengeluaran
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

    // Modul Laporan (Tahap 5b)
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');

    // Modul Pengaturan (Tahap 5c / D)
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Modul Notifikasi
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
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
