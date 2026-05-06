<?php

use App\Http\Controllers\Api\BudgetController as BudgetApiController;
use App\Http\Controllers\Api\TransactionDataController;
use App\Http\Controllers\Api\CategoryController as CategoryApiController;
use App\Http\Controllers\Api\CategoryDataController;
use App\Http\Controllers\Api\TransactionController as TransactionApiController;
use App\Http\Controllers\Api\BudgetDataController;
use Illuminate\Support\Facades\Route;

// Rutas API de SmartBudget.
Route::middleware(['web', 'auth'])->group(function () {

    // Transacciones — endpoint para DataTables serverSide
    Route::get('/transactions', [TransactionDataController::class, 'index'])
        ->name('api.transactions.index');
    Route::post('/transactions', [TransactionApiController::class, 'store'])
        ->name('transactions.store');
    Route::put('/transactions/{transaction}', [TransactionApiController::class, 'update'])
        ->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionApiController::class, 'destroy'])
        ->name('transactions.destroy');

    // Categorías — endpoint para DataTables serverSide
    Route::get('/categories', [CategoryDataController::class, 'index'])
        ->name('api.categories.index');
    Route::post('/categories', [CategoryApiController::class, 'store'])
        ->name('categories.store');
    Route::put('/categories/{category}', [CategoryApiController::class, 'update'])
        ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy'])
        ->name('categories.destroy');

    // Presupuestos — endpoint para DataTables serverSide
    Route::get('/budgets', [BudgetDataController::class, 'index'])
        ->name('api.budgets.index');
    Route::post('/budgets', [BudgetApiController::class, 'store'])
        ->name('budgets.store');
    Route::put('/budgets/{budget}', [BudgetApiController::class, 'update'])
        ->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetApiController::class, 'destroy'])
        ->name('budgets.destroy');
});