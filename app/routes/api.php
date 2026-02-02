<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptItemController;
use App\Http\Controllers\ReceiptParseController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('receipts')->group(function () {
    Route::get('/', [ReceiptController::class, 'index']);
    Route::get('/{receipt}', [ReceiptController::class, 'show']);
    Route::post('/{receipt}/items', [ReceiptItemController::class, 'store']);
    Route::post('/create', [ReceiptController::class, 'store']);
    Route::post('/parse/{receipt}', [ReceiptParseController::class, 'parse']);
});

Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::post('/create', [ItemController::class, 'store']);
});

Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::post('/create', [StoreController::class, 'store']);
    Route::delete('/{store}', [StoreController::class, 'destroy']);
});

Route::get('/error', function () {
    return response()->json(['message' => 'This is error'], 401);
});
