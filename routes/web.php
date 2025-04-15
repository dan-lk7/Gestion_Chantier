<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChantierController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
Route::resource('stocks', StockController::class);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('chantiers', ChantierController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/chantiers', [ChantierController::class, 'index'])->name('chantiers.index');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/chantiers/create', [ChantierController::class, 'create'])->name('chantiers.create');
    Route::post('/chantiers', [ChantierController::class, 'store'])->name('chantiers.store');
    Route::get('/chantiers/{chantier}/edit', [ChantierController::class, 'edit'])->name('chantiers.edit');
    Route::put('/chantiers/{chantier}', [ChantierController::class, 'update'])->name('chantiers.update');
    Route::delete('/chantiers/{chantier}', [ChantierController::class, 'destroy'])->name('chantiers.destroy');
});
Route::get('/movements/historique', [MovementController::class, 'historique'])->name('movements.historique');
Route::post('/stocks/transfer', [StockController::class, 'transfer'])->name('stocks.transfer');
Route::post('/stocks/use', [StockController::class, 'use'])->name('stocks.use');
Route::post('/transfer', [StockController::class, 'transfer'])->name('transfer.stock');
Route::get('/dashboard/responsable', [DashboardController::class, 'dashboardResponsable'])
    ->middleware('auth')
    ->name('dashboard.responsable');
Route::middleware(['auth'])->group(function () {
    Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
    Route::get('/movements/historique', [MovementController::class, 'historique'])->name('movements.historique');
});
    