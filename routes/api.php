<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('user')->group(function() {
        Route::Resource('ticket', TicketController::class, ['only' => ['index', 'show']]);
        Route::Resource('order', OrderController::class, ['only' => ['store', 'index', 'show']]);
        Route::Resource('transaction', TransactionController::class, ['only' => ['store', 'index', 'show']]);
        Route::Resource('account', AccountController::class, ['only' => ['index']]);
    });

    Route::prefix('admin')->group(function() {
        Route::Resource('ticket', TicketController::class);
        Route::Resource('order', OrderController::class);
        Route::Resource('transaction', TransactionController::class);
        Route::Resource('account', AccountController::class);
    });
});