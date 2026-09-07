<?php

use App\Http\Controllers\AdditionalFeeTypeController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\MoveOutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Portal\DocumentController;
use App\Http\Controllers\Portal\MaintenanceController;
use App\Http\Controllers\Portal\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TenantApplicationController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDocumentController;
use App\Http\Controllers\TenantPermissionController;
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
| pembayaran, pengeluaran, pengajuan sewa) bisa diakses Owner MAUPUN Admin ('staff').
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

    // Modul Pengajuan Sewa Kamar (F2.2)
    Route::resource('tenant-applications', TenantApplicationController::class)->only(['index', 'show']);
    Route::post('/tenant-applications/{tenantApplication}/review', [TenantApplicationController::class, 'review'])->name('tenant-applications.review');

    // Modul Kontrak
    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{contract}/activate', [ContractController::class, 'activate'])->name('contracts.activate');
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

    // Modul Perbaikan Kamar / Maintenance (F2.5)
    Route::get('/maintenance', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/{maintenance}', [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
    Route::patch('/maintenance/{maintenance}/status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance.update-status');

    // Modul Permohonan Izin Penghuni (F2.6)
    Route::get('/permissions', [TenantPermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [TenantPermissionController::class, 'show'])->name('permissions.show');
    Route::post('/permissions/{permission}/review', [TenantPermissionController::class, 'review'])->name('permissions.review');

    // Modul Move-Out & Pengakhiran Kontrak (F2.7)
    Route::get('/move-outs', [MoveOutController::class, 'index'])->name('move-outs.index');
    Route::get('/move-outs/{moveOut}', [MoveOutController::class, 'show'])->name('move-outs.show');
    Route::post('/move-outs/{moveOut}/review', [MoveOutController::class, 'review'])->name('move-outs.review');
    Route::post('/move-outs/{moveOut}/inspect', [MoveOutController::class, 'inspect'])->name('move-outs.inspect');
    Route::post('/move-outs/{moveOut}/finalize', [MoveOutController::class, 'finalize'])->name('move-outs.finalize');

    // Modul Dokumen Penghuni (F2.8)
    Route::get('/documents', [TenantDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [TenantDocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{document}/review', [TenantDocumentController::class, 'review'])->name('documents.review');
    Route::get('/documents/{document}/download', [TenantDocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [TenantDocumentController::class, 'preview'])->name('documents.preview');
    Route::delete('/documents/{document}', [TenantDocumentController::class, 'destroy'])->name('documents.destroy');

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

    // Eksplorasi & Pengajuan Kamar (F2.2)
    Route::get('/rooms', [App\Http\Controllers\Portal\RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [App\Http\Controllers\Portal\RoomController::class, 'show'])->name('rooms.show');
    Route::get('/applications', [App\Http\Controllers\Portal\TenantApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications', [App\Http\Controllers\Portal\TenantApplicationController::class, 'store'])->name('applications.store');
    Route::patch('/applications/{application}/cancel', [App\Http\Controllers\Portal\TenantApplicationController::class, 'cancel'])->name('applications.cancel');

    // Kontrak & Onboarding Agreement (F2.3)
    Route::get('/contract', [App\Http\Controllers\Portal\ContractController::class, 'index'])->name('contract.index');
    Route::post('/contract/{contract}/agreement', [App\Http\Controllers\Portal\ContractController::class, 'acceptAgreement'])->name('contract.agreement');

    // Tagihan & Pembayaran (F2.4)
    Route::get('/invoices', [App\Http\Controllers\Portal\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [App\Http\Controllers\Portal\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/payments', [App\Http\Controllers\Portal\PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::get('/payments', [App\Http\Controllers\Portal\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [App\Http\Controllers\Portal\PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/receipt', [App\Http\Controllers\Portal\PaymentController::class, 'receipt'])->name('payments.receipt');

    // Pemeliharaan & Perbaikan Kamar (F2.5)
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');

    // Permohonan Izin Penghuni (F2.6)
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::patch('/permissions/{permission}/cancel', [PermissionController::class, 'cancel'])->name('permissions.cancel');

    // Permohonan Keluar Kost / Move-Out (F2.7)
    Route::get('/move-outs', [App\Http\Controllers\Portal\MoveOutController::class, 'index'])->name('move-outs.index');
    Route::get('/move-outs/create', [App\Http\Controllers\Portal\MoveOutController::class, 'create'])->name('move-outs.create');
    Route::post('/move-outs', [App\Http\Controllers\Portal\MoveOutController::class, 'store'])->name('move-outs.store');
    Route::get('/move-outs/{moveOut}', [App\Http\Controllers\Portal\MoveOutController::class, 'show'])->name('move-outs.show');
    Route::patch('/move-outs/{moveOut}/cancel', [App\Http\Controllers\Portal\MoveOutController::class, 'cancel'])->name('move-outs.cancel');

    // Dokumen & Berkas Penghuni (F2.8)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Profil Mandiri
    Route::get('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'update'])->name('profile.update');
});

// ── Manajemen User — HANYA Owner ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'owner'])->group(function () {

    // CRUD akun user (Resource Controller)
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
