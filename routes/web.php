<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\BarangaySettingController;
use App\Http\Controllers\BarangayProfileController;

Route::redirect('/', '/login');

Auth::routes();

// ==========================================
// 1. GENERAL GROUP (Accessible by Admin, Secretary, & Staff)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/settings/update-signature-ajax', [App\Http\Controllers\BarangaySettingController::class, 'updateSignatureAjax'])->name('settings.update.ajax');
    // Resident Master File (View & Create for everyone)
    Route::get('/residents', [ResidentController::class, 'index'])->name('residents.index');
    Route::get('/residents/create', [ResidentController::class, 'create'])->name('residents.create');
    Route::post('/residents', [ResidentController::class, 'store'])->name('residents.store');

    // Certificate Generation & History
    Route::get('/residents/{resident}/certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::get('/certificates/history', [CertificateController::class, 'history'])->name('certificates.history');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/history/print', [CertificateController::class, 'printHistory'])->name('certificates.history.print');
    Route::get('/dashboard/search-residents', [DashboardController::class, 'searchResidents'])->name('dashboard.search-residents');
});


// ==========================================
// 2. SECRETARY & ADMIN GROUP (Manage Templates & Archives)
// ==========================================
Route::middleware(['auth', 'role:Admin,Secretary'])->group(function () {
    // 1. PLACE STATIC ARCHIVE ROUTES FIRST (Before any {resident} parameters)
    Route::get('/archived-residents', [ResidentController::class, 'archived'])->name('residents.archived');
    Route::patch('/residents/{id}/restore', [ResidentController::class, 'restore'])->name('residents.restore');

    // Certificate Templates
    Route::get('/templates/{type}', [CertificateTemplateController::class, 'show'])->name('templates.edit');
    Route::get('/templates/{type}/edit', [CertificateTemplateController::class, 'edit'])->name('templates.body');
    Route::put('/templates/{type}', [CertificateTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{type}', [CertificateTemplateController::class, 'reset'])->name('templates.reset');
    
    // Resident Management (Edit, Update, Destroy / Archive)
    Route::get('/residents/{resident}/edit', [ResidentController::class, 'edit'])->name('residents.edit');
    Route::put('/residents/{resident}', [ResidentController::class, 'update'])->name('residents.update');
    Route::delete('/residents/{resident}', [ResidentController::class, 'destroy'])->name('residents.destroy');

    // Barangay Settings
    Route::get('/settings/barangay', [BarangaySettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/barangay', [BarangaySettingController::class, 'update'])->name('settings.update');
});

// ==========================================
// 3. ADMIN-ONLY GROUP (Logs & Users)
// ==========================================
Route::middleware(['auth', 'role:Admin'])->group(function () {
    // System Audit Logs
    Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/print', [AuditLogController::class, 'print'])->name('logs.print');

    // User Management
    Route::resource('users', UserController::class);

    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    // Barangay name (changes every place the barangay name is shown)
    Route::get('/barangay', [BarangayProfileController::class, 'edit'])->name('barangay.edit');
    Route::put('/barangay', [BarangayProfileController::class, 'update'])->name('barangay.update');
});