<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Medical\DashboardController as MedicalDashboardController;

// Root route - redirect to login or dashboard based on authentication
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Admin route - redirect to admin login or dashboard based on authentication
Route::get('/admin', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } else {
            // If user is logged in but not admin, logout and redirect to admin login
            \Illuminate\Support\Facades\Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Please login with admin credentials.');
        }
    }
    return redirect()->route('admin.login');
});

// Default dashboard route - redirect based on role
Route::get('dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    
    if ($user && method_exists($user, 'hasRole')) {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole(['surgeon', 'staff'])) {
            return redirect()->route('medical.dashboard');
        }
    }
    
    // Fallback to default dashboard
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes - only accessible by admin role  
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Surgeon Management Routes
    Route::get('/surgeon/register', \App\Livewire\Admin\SurgeonRegistration::class)->name('surgeon.register');
    Route::get('/surgeon/list', \App\Livewire\Admin\SurgeonList::class)->name('surgeon.list');
    
    // Account Management Routes (Financial)
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/wallet', [\App\Http\Controllers\Admin\AccountController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/add-funds', [\App\Http\Controllers\Admin\AccountController::class, 'addFunds'])->name('wallet.add-funds');
        Route::get('/transactions', [\App\Http\Controllers\Admin\AccountController::class, 'transactions'])->name('transactions');
        Route::get('/orders', [\App\Http\Controllers\Admin\AccountController::class, 'orders'])->name('orders');
        Route::get('/reports', [\App\Http\Controllers\Admin\AccountController::class, 'reports'])->name('reports');
    });

    // Product Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{id}/restore', [\App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Admin\ProductController::class, 'forceDelete'])->name('force-delete');
    });
    
    // System Settings
    Route::get('/settings', \App\Livewire\Admin\SystemSettings::class)->name('settings');
    
    // Purchase Order Management Routes
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/history', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'history'])->name('history');
        Route::get('/inventory', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'inventory'])->name('inventory');
        Route::get('/{order}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/confirm', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'confirm'])->name('confirm');
        Route::patch('/{order}/cancel', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'cancel'])->name('cancel');
        Route::patch('/{order}/deliver', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'markAsDelivered'])->name('deliver');
    });
});

// Medical routes - accessible by surgeon and staff roles
Route::middleware(['auth', 'role:surgeon|staff'])->prefix('medical')->name('medical.')->group(function () {
    Route::get('/dashboard', [MedicalDashboardController::class, 'index'])->name('dashboard');
    
    // Patient Management Routes
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Medical\PatientController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Medical\PatientController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Medical\PatientController::class, 'store'])->name('store');
        Route::get('/{patient}', [\App\Http\Controllers\Medical\PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [\App\Http\Controllers\Medical\PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [\App\Http\Controllers\Medical\PatientController::class, 'update'])->name('update');
        Route::patch('/{patient}/toggle-status', [\App\Http\Controllers\Medical\PatientController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Staff Management Routes (only accessible by surgeons)
    Route::middleware('role:surgeon')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Medical\StaffController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Medical\StaffController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Medical\StaffController::class, 'store'])->name('store');
        Route::get('/trashed', [\App\Http\Controllers\Medical\StaffController::class, 'trashed'])->name('trashed');
        Route::get('/{staff}', [\App\Http\Controllers\Medical\StaffController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [\App\Http\Controllers\Medical\StaffController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [\App\Http\Controllers\Medical\StaffController::class, 'update'])->name('update');
        Route::delete('/{staff}', [\App\Http\Controllers\Medical\StaffController::class, 'destroy'])->name('destroy');
        Route::patch('/{staff}/toggle-status', [\App\Http\Controllers\Medical\StaffController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{id}/restore', [\App\Http\Controllers\Medical\StaffController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Medical\StaffController::class, 'forceDelete'])->name('force-delete');
    });
    
    // Purchase Order Routes
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/products', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'products'])->name('products');
        Route::get('/create', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/inventory', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'inventory'])->name('inventory');
        Route::get('/{order}', [\App\Http\Controllers\Medical\PurchaseOrderController::class, 'show'])->name('show');
    });

    // Product selling price management (doctors only)
    Route::middleware('role:surgeon')->patch('/products/{product}/selling-price', [\App\Http\Controllers\Medical\ProductController::class, 'updateSellingPrice'])->name('products.update-selling-price');

    // Sales Order Routes (for staff creating orders for patients)
    Route::prefix('sales-orders')->name('sales-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Medical\SalesOrderController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Medical\SalesOrderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Medical\SalesOrderController::class, 'store'])->name('store');
        Route::get('/{salesOrder}', [\App\Http\Controllers\Medical\SalesOrderController::class, 'show'])->name('show');
    });

    // Recurring Order Routes (for staff managing recurring patient orders)
    Route::prefix('recurring-orders')->name('recurring-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'store'])->name('store');
        Route::get('/due', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'due'])->name('due');
        Route::get('/{recurringOrder}', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'show'])->name('show');
        Route::get('/{recurringOrder}/edit', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'edit'])->name('edit');
        Route::put('/{recurringOrder}', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'update'])->name('update');
        Route::post('/{recurringOrder}/process', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'process'])->name('process');
        Route::patch('/{recurringOrder}/toggle-status', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{recurringOrder}/pause', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'pause'])->name('pause');
        Route::patch('/{recurringOrder}/resume', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'resume'])->name('resume');
        Route::patch('/{recurringOrder}/cancel', [\App\Http\Controllers\Medical\RecurringOrderController::class, 'cancel'])->name('cancel');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
