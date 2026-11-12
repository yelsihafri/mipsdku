
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'order-service',
    ], 200);
});

// Autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Order API
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Checkout menggunakan POST /orders
    Route::post('/orders', [OrderController::class, 'checkout']);

    // Daftar pesanan pengguna
    Route::get('/orders/user/{userId}', [OrderController::class, 'userOrders']);

    // Daftar dan statistik pesanan
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/statistics', [OrderController::class, 'statistics']);

    // Detail dan pembayaran
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/pay', [OrderController::class, 'pay']);

    // Endpoint lama tetap dipertahankan
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});
