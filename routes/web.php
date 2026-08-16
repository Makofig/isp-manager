<?php

use Illuminate\Support\Facades\Route;

// Import controladores 
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\AccessPointController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mp/success', fn () => 'OK')->name('mp.success');
Route::get('/mp/failure', fn () => 'FAIL')->name('mp.failure');
Route::get('/mp/pending', fn () => 'PENDING')->name('mp.pending');
Route::post('/mp/webhook', fn () => 'WEBHOOK')->name('mp.webhook');

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
])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/clients/banned', [ClientController::class, 'banned'])->name('clients.banned');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/clients/edit/{id}', [ClientController::class, 'edit'])->name('clients.edit');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::put('/clients/update/{id}', [ClientController::class, 'update'])->name('clients.update');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::delete('/clients/destroy/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/clients/show/{id}', [ClientController::class, 'show'])->name('clients.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (){
    Route::get('/clients/export/{type}', [ClientController::class, 'exportPdf'])->name('clients.export');
});

// Ruta de los access points 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/access-point', [AccessPointController::class, 'index'])->name('access-point');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/access-point/store', [AccessPointController::class, 'store'])->name('access-point.store');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/access-point/create', [AccessPointController::class, 'create'])->name('access-point.create');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/access-point/show/{id}', [AccessPointController::class, 'show'])->name('access-point.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/access-point/edit/{id}', [AccessPointController::class, 'edit'])->name('access-point.edit');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::put('/access-point/update/{id}', [AccessPointController::class, 'update'])->name('access-point.update');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::delete('/access-point/destroy/{id}', [AccessPointController::class, 'destroy'])->name('access-point.destroy');
});

// Ruta de los planes 
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/contracts/store', [ContractController::class, 'store'])->name('contracts.store');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/contracts/edit/{id}', [ContractController::class, 'edit'])->name('contracts.edit');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::put('/contracts/update/{id}', [ContractController::class, 'update'])->name('contracts.update');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::delete('/contracts/destroy/{id}', [ContractController::class, 'destroy'])->name('contracts.destroy');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/quota', [QuotaController::class, 'index'])->name('quota');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/quota/create', [QuotaController::class, 'create'])->name('quota.create');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/quota/store', [QuotaController::class, 'store'])->name('quota.store');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/clients/debtors', [ClientController::class, 'debtors'])->name('debtors');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/statistics', function () {
        return view('statistics');
    })->name('statistics');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/payments/show/{id}', [PaymentController::class, 'show'])->name('payments.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/payments/edit/{id}', [PaymentController::class, 'edit'])->name('payments.edit');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::put('/payments/update/{id}', [PaymentController::class, 'update'])->name('payments.update');
});