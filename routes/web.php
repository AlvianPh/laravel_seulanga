<?php

use App\Http\Controllers\AdditionalFeeTypeController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Struktur middleware:
|   - 'auth'   : wajib login (Breeze default)
|   - 'staff'  : hanya Role Owner dan Admin (EnsureStaff middleware)
|   - 'owner'  : hanya Role Owner (EnsureOwner middleware)
|   - 'tenant' : hanya Role Tenant (EnsureTenant middleware)
|
| Modul operasional (dashboard, kamar, penghuni, kontrak, tagihan,
| pembayaran, pengeluaran) bisa diakses Owner MAUPUN Admin ('staff').
|
| Manajemen User (CRUD akun) HANYA bisa diakses Owner ('owner').
|
| Portal Penghuni (/portal/*) HANYA bisa diakses Tenant ('tenant').
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ── Modul yang bisa diakses Staff (Owner DAN Admin) ──────────────────────
Route::middleware(['auth', 'verified', 'staff'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Staff (bawaan Breeze — edit profil diri sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul Kamar
    Route::resource('rooms', RoomController::class)->withTrashed(['show']);
    Route::delete('/rooms/{room}/photos/{photo}', [RoomController::class, 'deletePhoto'])->name('rooms.photos.destroy');
    Route::patch('/rooms/{room}/photos/{photo}/primary', [RoomController::class, 'setPrimaryPhoto'])->name('rooms.photos.primary');

    // Master Data Referensi
    Route::resource('room_types', RoomTypeController::class);
    Route::resource('facilities', FacilityController::class);
    Route::resource('payment_methods', PaymentMethodController::class)->except(['show']);
    Route::resource('expense_categories', ExpenseCategoryController::class)->except(['show']);
    Route::resource('bank_accounts', BankAccountController::class)->except(['show']);
    Route::resource('additional_fee_types', AdditionalFeeTypeController::class)->except(['show']);

    // Modul Penghuni
    Route::resource('tenants', TenantController::class)->withTrashed(['show']);
    Route::delete('/tenants/{tenant}/ktp', [TenantController::class, 'deleteKtp'])->name('tenants.ktp.destroy');
    Route::delete('/tenants/{tenant}/photo', [TenantController::class, 'deletePhoto'])->name('tenants.photo.destroy');

    // Modul Kontrak
    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{contract}/renew', [ContractController::class, 'renew'])->name('contracts.renew');
    Route::post('/contracts/{contract}/terminate', [ContractController::class, 'terminate'])->name('contracts.terminate');

    // Modul Tagihan
    Route::post('/invoices/generate-manual', [InvoiceController::class, 'generateManual'])->name('invoices.generate-manual');
    Route::resource('invoices', InvoiceController::class)->except(['create', 'store']);

    // Modul Pembayaran
    Route::get('/payments/{payment}/verify', [PaymentController::class, 'verifyForm'])->name('payments.verify');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'processVerification'])->name('payments.process-verification');
    Route::resource('payments', PaymentController::class)->except(['edit', 'update', 'destroy']);

    // Modul Pengeluaran
    Route::resource('expenses', ExpenseController::class);

    // Modul Laporan (Tahap 5b)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Modul Pengaturan (Tahap 5c / D)
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Modul Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// ── Portal Khusus Penghuni (Tenant) ──────────────────────────────────────
Route::middleware(['auth', 'verified', 'tenant'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'update'])->name('profile.update');
});

// ── Manajemen User — HANYA Owner ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'owner'])->group(function () {

    // CRUD akun user (Resource Controller)
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
