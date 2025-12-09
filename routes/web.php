<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\WorkInstructionController;
use App\Http\Controllers\Admin\WorkInstructionReportController;
use App\Http\Controllers\Admin\WorkInstructionBulkReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WarehouseStaff\StockController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\User\WorkInstructionController as UserWorkInstructionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $workInstructions = \App\Models\WorkInstruction::all();
        
        $stats = [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'total_items' => \App\Models\Item::count(),
            'low_stock_items' => \App\Models\Item::whereRaw('current_stock <= minimum_stock')->count(),
            'pending_wi' => $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'not_started')->count(),
            'overdue_wi' => $workInstructions->filter(fn($wi) => $wi->getMainStatus() === 'overdue')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');

    // Items
    Route::resource('items', ItemController::class);

    // Locations
    Route::resource('locations', LocationController::class);

    // Work Instruction Bulk Reports (must be before resource routes)
    Route::get('work-instructions/bulk-report', [WorkInstructionBulkReportController::class, 'index'])->name('work-instructions.bulk-report.index');
    Route::post('work-instructions/bulk-report/generate', [WorkInstructionBulkReportController::class, 'generatePdf'])->name('work-instructions.bulk-report.generate');
    
    // Work Instructions
    Route::resource('work-instructions', WorkInstructionController::class);
    Route::get('work-instructions/{workInstruction}/report-pdf', [WorkInstructionReportController::class, 'generatePdf'])->name('work-instructions.report-pdf');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('reports/stock/export', [ReportController::class, 'stockExport'])->name('reports.stock-export');
});

// Warehouse Staff Routes
Route::middleware(['auth', 'warehouse_staff'])->prefix('warehouse-staff')->name('warehouse-staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');
    
    // Stock Management
    Route::get('/stock-in', [StockController::class, 'stockInForm'])->name('stock-in');
    Route::post('/stock-in', [StockController::class, 'stockIn'])->name('stock-in');
    Route::get('/stock-out', [StockController::class, 'stockOutForm'])->name('stock-out');
    Route::post('/stock-out', [StockController::class, 'stockOut'])->name('stock-out');
    
    // Stock History
    Route::get('/stock-history', [StockController::class, 'stockHistory'])->name('stock-history');
});

// User Routes
Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $workInstructions = \App\Models\WorkInstruction::where('assigned_user_id', auth()->id())
            ->with(['items.itemLocations', 'statusProgress'])
            ->orderBy('deadline', 'asc')
            ->get();
        
        // Update status untuk semua WI user
        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }
        
        return view('user.dashboard', compact('workInstructions'));
    })->name('dashboard');

    // Work Instructions
    Route::get('work-instructions', [UserWorkInstructionController::class, 'index'])->name('work-instructions.index');
    Route::get('work-instructions/{workInstruction}', [UserWorkInstructionController::class, 'show'])->name('work-instructions.show');
    
    // Work Instruction Actions
    Route::post('work-instructions/{workInstruction}/update-item', [UserWorkInstructionController::class, 'updateItem'])->name('work-instructions.update-item');
    Route::post('work-instructions/{workInstruction}/complete', [UserWorkInstructionController::class, 'complete'])->name('work-instructions.complete');
});