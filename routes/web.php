<?php

use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Homepage
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::prefix('dashboard')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('food', FoodController::class);


    Route::get('transaction/{id}/status/{status}', [TransactionController::class, 'changeStatus'])
        ->name('transaction.changeStatus');
    Route::resource('transaction', TransactionController::class);
});

// Midtrans Page Success, Unfinish, Error
// Route::get('midtrans/success', [MidtransController::class, 'success']);
// Route::get('midtrans/unfinish', [MidtransController::class, 'unfinish']);
// Route::get('midtrans/error', [MidtransController::class, 'error']);
