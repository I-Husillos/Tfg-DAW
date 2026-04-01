<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AiController;
use Illuminate\Support\Facades\Route;

//  Rutas públicas 
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

//  Logout 
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

//  Rutas autenticadas 
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Transacciones
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])
        ->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])
        ->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])
        ->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])
        ->name('transactions.destroy');

    // Categorías
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    // Presupuestos
    Route::get('/budgets', [BudgetController::class, 'index'])
        ->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])
        ->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])
        ->name('budgets.store');
    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])
        ->name('budgets.edit');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])
        ->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])
        ->name('budgets.destroy');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Informes
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf');

    Route::post('/ai/ask', [AiController::class, 'ask'])->name('ai.ask');
    Route::post('/ai/clear', [AiController::class, 'clear'])->name('ai.clear');
});