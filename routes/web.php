<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectricBillController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admins CRUD
    Route::resource('admins', AdminController::class)->except(['show']);
    Route::post('/admins/{admin}/restore', [AdminController::class, 'restore'])->name('admins.restore');

    // Tenants
    // Add these BEFORE your resource routes
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants/lease-form', [TenantController::class, 'leaseForm'])->name('tenants.lease-form');
    Route::post('/tenants/agreement-preview', [TenantController::class, 'agreementPreview'])->name('tenants.agreement-preview'); // ← This was missing!
    Route::post('/tenants/final-preview', [TenantController::class, 'finalPreview'])->name('tenants.final-preview');
    Route::post('/tenants/store-with-lease', [TenantController::class, 'storeWithLease'])->name('tenants.store-with-lease');
    Route::resource('tenants', TenantController::class);
    Route::get('/tenants/{tenant}/leases/create', [LeaseController::class, 'create'])->name('leases.create');
    Route::post('/tenants/{tenant}/leases', [LeaseController::class, 'store'])->name('leases.store');
    Route::post('/tenants/{tenant}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
    Route::delete('/tenants/{tenant}/delete', [TenantController::class, 'delete'])->name('tenants.delete');

    // Leases
    Route::resource('leases', LeaseController::class)->except(['create', 'store']);
    Route::post('/tenants/{tenant}/leases/preview', [LeaseController::class, 'preview'])->name('leases.preview');
    // Lease Agreement PDF
    Route::get('/leases/{lease}/download-agreement', [LeaseController::class, 'downloadAgreement'])
        ->name('lease-agreement.download');

    Route::post('/leases/{lease}/pay-in-full', [LeaseController::class, 'payLeaseInFull'])
        ->name('leases.pay-in-full');

    // Payments
    Route::get('/lease-payments/{payment}/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/lease-payments/{payment}/pay', [PaymentController::class, 'store'])->name('payments.store');

    // PDF
    Route::get('/lease-payments/{payment}/invoice', [PaymentController::class, 'downloadInvoice'])->name('payments.invoice');
    Route::get('/lease-payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');

    // AJAX routes for lease-based filtering
    Route::get('/tenants/{tenant}/leases/filter-lease', [TenantController::class, 'filterLeasesByLease'])->name('tenants.filter-leases-lease');
    Route::get('/tenants/{tenant}/payments/filter-lease', [TenantController::class, 'filterPaymentsByLease'])->name('tenants.filter-payments-lease');

    // Room management
    Route::resource('rooms', RoomController::class)->except(['show', 'edit', 'update']);
    Route::post('/rooms/{room}/add-bed', [RoomController::class, 'addBed'])->name('rooms.add-bed');
    Route::post('/rooms/{room}/remove-bed', [RoomController::class, 'removeBed'])->name('rooms.remove-bed');

    // Electric Bills Management
    Route::get('/electric-bills', [ElectricBillController::class, 'index'])->name('electric-bills.index');
    Route::post('/electric-bills', [ElectricBillController::class, 'store'])->name('electric-bills.store');
    Route::get('/electric-bills/room/{room}', [ElectricBillController::class, 'roomBills'])->name('electric-bills.room');
    Route::put('/electric-bills/{bill}', [ElectricBillController::class, 'update'])->name('electric-bills.update');

    // Grouping for clarity
    Route::prefix('reports')->name('reports.')->group(function () {
        // The view route (for the dashboard/table)
        Route::get('/income', [ReportController::class, 'incomeReport'])->name('income');

        // The download route (for the Excel file)
        Route::get('/income/download', [ReportController::class, 'downloadIncomeExcel'])->name('income.download');
    });
});

require __DIR__.'/auth.php';
