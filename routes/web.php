<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. PUBLIC ROUTES
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// 2. COMMON AUTHENTICATED ROUTES (Accessible by All Logged-in Users)
Route::middleware('auth')->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // --- DASHBOARD (Controller handles Role redirection) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ATTENDANCE (Clock In/Out Action) ---
    // Accessible by everyone to clock themselves in
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // --- PERSONAL REPORTS ---
    Route::get('/reports/my-export', [ReportController::class, 'exportMySalesPdf'])->name('reports.my_export');
});

// 3. POS & SALES ROUTES (Accessible by Admin & Cashier)
// Note: Ensure your 'role' middleware handles comma-separated values, or use a custom gate.
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {

    // --- POS TERMINAL ---
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');

    // --- RECEIPT VIEW ---
    Route::get('/sales/{sale}/receipt', function (\App\Models\Sale $sale) {
        return view('pos.receipt', compact('sale'));
    })->name('sales.receipt');

    // --- KHQR PAYMENTS ---
    Route::post('/pos/generate-qr', [PosController::class, 'generateKhqr'])->name('pos.generate_qr');
    Route::post('/pos/check-qr', [PosController::class, 'checkKhqrStatus'])->name('pos.check_qr');

    // --- SALES REPORTS (View Only) ---
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
});

// 4. INVENTORY ROUTES (Accessible by Admin & Stock Manager)
Route::middleware(['auth', 'role:admin,stock_manager'])->group(function () {

    // --- PRODUCT & CATEGORY MANAGEMENT ---
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // --- STOCK REPORTS ---
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
});

// 3. POS & SALES ROUTES (Accessible by Admin, Cashier, AND Stock Manager)
// Update: Added 'stock_manager' to the list
Route::middleware(['auth', 'role:admin,cashier,stock_manager'])->group(function () {

    // --- POS TERMINAL ---
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/products/stock', [PosController::class, 'stock'])->name('pos.products.stock');
    Route::get('/pos/products/promotions', [PosController::class, 'promotions'])->name('pos.products.promotions');

    // --- RECEIPT VIEW ---
    Route::get('/sales/{sale}/receipt', function (\App\Models\Sale $sale) {
        return view('pos.receipt', compact('sale'));
    })->name('sales.receipt');

    // --- KHQR PAYMENTS ---
    Route::post('/pos/generate-qr', [PosController::class, 'generateKhqr'])->name('pos.generate_qr');
    Route::post('/pos/check-qr', [PosController::class, 'checkKhqrStatus'])->name('pos.check_qr');

    // --- SALES REPORTS (View Only) ---
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');

    // --- PROMOTIONS (View Only for Cashier) ---
    Route::resource('promotions', PromotionController::class)->only(['index']);
});

// 4. PROMOTIONS MANAGEMENT (Admin & Stock Manager)
Route::middleware(['auth', 'role:admin,stock_manager'])->group(function () {
    Route::resource('promotions', PromotionController::class)->except(['index', 'show']);
});

// 5. ADMIN ONLY ROUTES (Restricted High-Level Access)
Route::middleware(['auth', 'role:admin'])->group(function () {

    // --- HR & STRUCTURE ---
    Route::resource('positions', PositionController::class);
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees/{employee}/report', [EmployeeController::class, 'report'])->name('employees.report');
    Route::get('/activities', [ActivityLogController::class, 'index'])->name('activities.index');

    // --- ATTENDANCE MANAGEMENT  ---
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/export', [AttendanceController::class, 'exportPdf'])->name('attendance.export');
    Route::put('/attendance/checkout/{attendance}', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // --- REPORTS ---
    Route::get('/reports/export', [ReportController::class, 'exportPdf'])->name('reports.export');
});
