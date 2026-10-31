<?php

use Illuminate\Support\Facades\Route;

// Import controladores 
use App\Http\Controllers\HealthController;
use App\Http\Controllers\clientController; 
use App\Http\Controllers\contractController;
use App\Http\Controllers\access_pointController;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\quotaController;
use App\Http\Controllers\NotificationController;
use App\Livewire\QuotaCreate;

// Health check — public, no auth required
Route::get('/health', HealthController::class)->name('health');

Route::get('/', function () {
    return view('welcome');
})->middleware('throttle:api');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Rutas de los clientes 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:auth-users', 
])->prefix('clients')->group(function () {
    Route::get('/', [clientController::class, 'index'])->name('clients');
    Route::get('/create', [clientController::class, 'create'])->name('clients.create');
    Route::post('/store', [clientController::class, 'store'])->name('clients.store');
    Route::get('/banned', [clientController::class, 'banned'])->name('clients.banned');
    Route::get('/edit/{id}', [clientController::class, 'edit'])->name('clients.edit');
    Route::put('/update/{id}', [clientController::class, 'update'])->name('clients.update');
    Route::delete('/destroy/{id}', [clientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/show/{id}', [clientController::class, 'show'])->name('clients.show');
    Route::get('/export/{type}', [clientController::class, 'exportPdf'])->name('clients.export');
    Route::get('/debtors', [clientController::class, 'debtors'])->name('debtors');
});

// Ruta de los access points 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:auth-users',
])->prefix('access-point')->group(function () {
    Route::get('/', [access_pointController::class, 'index'])->name('access-point');
    Route::post('/store', [access_pointController::class, 'store'])->name('access-point.store');
    Route::get('/create', [access_pointController::class, 'create'])->name('access-point.create');
    Route::get('/show/{id}', [access_pointController::class, 'show'])->name('access-point.show');
    Route::get('/edit/{id}', [access_pointController::class, 'edit'])->name('access-point.edit');
    Route::put('/update/{id}', [access_pointController::class, 'update'])->name('access-point.update');
    Route::delete('/destroy/{id}', [access_pointController::class, 'destroy'])->name('access-point.destroy');
});  

// Ruta de los planes 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:auth-users',
])->prefix('contracts')->group(function () {
    Route::get('/', [contractController::class, 'index'])->name('contracts');
    Route::get('/create', [contractController::class, 'create'])->name('contracts.create');
    Route::post('/store', [contractController::class, 'store'])->name('contracts.store');
    Route::get('/edit/{id}', [contractController::class, 'edit'])->name('contracts.edit');
    Route::put('/update/{id}', [contractController::class, 'update'])->name('contracts.update');
    Route::delete('/destroy/{id}', [contractController::class, 'destroy'])->name('contracts.destroy');
});

// Rutas de las cuotas 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:auth-users',
])->prefix('quota')->group(function () {
    Route::get('/', [quotaController::class, 'index'])->name('quota');
    Route::get('/create', QuotaCreate::class)->name('quota.create');
    #Route::get('/create', [quotaController::class, 'create'])->name('quota.create');
    Route::post('/store', [quotaController::class, 'store'])->name('quota.store');
});

// Rutas de los pagos 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:auth-users',
])->prefix('payments')->group(function () {
    Route::get('/show/{id}', [paymentController::class, 'show'])->name('payments.show');
    Route::get('/edit/{id}', [paymentController::class, 'edit'])->name('payments.edit');
    Route::put('/update/{id}', [paymentController::class, 'update'])->name('payments.update');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:stats-heavy',
])->group(function () {
    Route::get('/statistics', function () {
        return view('statistics');
    })->name('statistics');
});

// Rutas de notificaciones
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// Ruta Admin (placeholder)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/admin', fn () => view('admin.index'))->name('admin');
});