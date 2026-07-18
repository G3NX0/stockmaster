<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// PWA Assets (Publicly Accessible)
Route::get('/manifest.json', function() {
    return response()->file(public_path('manifest.json'));
});
Route::get('/sw.js', function() {
    return response()->file(public_path('sw.js'));
});

// Landing Page (Public E-Commerce Store)
Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::redirect('/store', '/');

Route::post('/store/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::post('/store/login', [StoreController::class, 'login'])->name('store.login');
Route::post('/store/register', [StoreController::class, 'register'])->name('store.register');
Route::post('/store/profile', [StoreController::class, 'saveProfile'])->name('store.profile.save');
Route::get('/store/orders', [StoreController::class, 'getOrders'])->name('store.orders');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Shared Routes (Admin & Staff)
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/poll', [DashboardController::class, 'poll'])->name('dashboard.poll');
        Route::post('/dashboard/send-whatsapp-report', [DashboardController::class, 'sendWhatsappReport'])->name('dashboard.send-whatsapp-report');
        Route::get('scanner', [DashboardController::class, 'scanner'])->name('scanner');
        Route::get('guide', function() { return view('guide'); })->name('guide');
        
        // Export routes
        Route::middleware(['role:admin'])->group(function () {
            Route::get('items/export-pdf', [ItemController::class, 'exportPdf'])->name('items.export-pdf');
            Route::get('items/export-excel', [ItemController::class, 'exportExcel'])->name('items.export-excel');
            Route::post('items/import-excel', [ItemController::class, 'importExcel'])->name('items.import-excel');
        });

        Route::get('items/{item}/print-label', [ItemController::class, 'printLabel'])->name('items.print-label');
        Route::resource('items', ItemController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('units', UnitController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('transactions', TransactionController::class);
        
        // Admin & Specialized Roles
        Route::middleware(['role:admin'])->group(function () {
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('logs.index');
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/forecasting', [ReportController::class, 'forecasting'])->name('reports.forecasting');
            Route::get('reports/heatmap', [ReportController::class, 'heatmap'])->name('reports.heatmap');
            Route::get('reports/expiring', [ReportController::class, 'expiringItems'])->name('reports.expiring');
            Route::get('reports/profit', [ReportController::class, 'profitMargin'])->name('reports.profit');
            
            // Tier 3: Enterprise Ecosystem
            Route::resource('customers', CustomerController::class);
            Route::resource('transfers', TransferController::class)->only(['index', 'store']);
            Route::get('pos/generate', [PurchaseOrderController::class, 'generate'])->name('pos.generate');
            Route::get('pos/restock/{item}', [PurchaseOrderController::class, 'restockItem'])->name('pos.restock');
            Route::post('pos/{po}/status', [PurchaseOrderController::class, 'updateStatus'])->name('pos.update-status');
            Route::resource('pos', PurchaseOrderController::class)->only(['index', 'show']);

            // Asset & Reconciliation
            Route::resource('enterprise-assets', AssetController::class)->names('assets');
            Route::post('assets/{item}/toggle', [AssetController::class, 'toggle'])->name('assets.toggle');
            Route::resource('reconciliations', ReconciliationController::class)->only(['index', 'create', 'store']);
            
            // Batches & Notes
            Route::post('items/{item}/batches', [BatchController::class, 'store'])->name('batches.store');
            Route::delete('batches/{batch}', [BatchController::class, 'destroy'])->name('batches.destroy');
            Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
            Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
            
            // System Settings
            Route::get('settings/users', [UserController::class, 'index'])->name('users.index');
            Route::patch('settings/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::get('settings/backups', [BackupController::class, 'index'])->name('backups.index');
            Route::post('settings/backups', [BackupController::class, 'create'])->name('backups.create');
            Route::get('settings/backups/download/{file}', [BackupController::class, 'download'])->name('backups.download');
            Route::delete('settings/backups/{file}', [BackupController::class, 'destroy'])->name('backups.destroy');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');

        // Granular Prototype Routes
        Route::prefix('prototype/zen')->group(function() {
            $mockData = function() {
                return [
                    'totalItems' => 1248,
                    'totalStock' => 8540,
                    'totalValue' => 245000000,
                    'lowStockCount' => 12,
                    'recentTransactions' => collect([
                        (object)['type' => 'in', 'item' => (object)['nama_barang' => 'MacBook Pro M3'], 'quantity' => 5, 'created_at' => now()->subMinutes(15)],
                        (object)['type' => 'out', 'item' => (object)['nama_barang' => 'Sony A7 IV'], 'quantity' => 2, 'created_at' => now()->subHours(2)],
                        (object)['type' => 'in', 'item' => (object)['nama_barang' => 'DJI Mic 2'], 'quantity' => 10, 'created_at' => now()->subHours(5)],
                    ]),
                    'last7Days' => collect(range(0, 6))->map(function($i) {
                        return (object)[
                            'date' => now()->subDays(6-$i)->format('D'),
                            'in' => rand(10, 50),
                            'out' => rand(5, 40)
                        ];
                    })
                ];
            };

            Route::get('/', function() use ($mockData) {
                return view('prototype.zen_glass', $mockData());
            })->name('prototype.zen');

            Route::get('v5', function() use ($mockData) {
                return view('prototype.zen.v5_typo_comp', $mockData());
            })->name('prototype.zen.v5');

            Route::get('v10', function() use ($mockData) {
                return view('prototype.zen.v10_masterpiece', $mockData());
            })->name('prototype.zen.v10');

            Route::get('v12', function() use ($mockData) {
                return view('prototype.zen.v12_living', $mockData());
            })->name('prototype.zen.v12');

            Route::get('v12_2', function() use ($mockData) {
                return view('prototype.zen.v12_2_scalable', $mockData());
            })->name('prototype.zen.v12_2');

            Route::get('v12_3', function() use ($mockData) {
                return view('prototype.zen.v12_3_fluid', $mockData());
            })->name('prototype.zen.v12_3');

            Route::get('v12_4', function() use ($mockData) {
                return view('prototype.zen.v12_4_perfection', $mockData());
            })->name('prototype.zen.v12_4');
        });
    });
});
