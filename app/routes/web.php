<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptItemController;
use App\Http\Controllers\ReceiptParseController;
use App\Http\Controllers\StoreController;
use App\Models\Receipt;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/receipts/new', [ReceiptController::class, 'create'])->name('receipts.new');

Route::post('/receipts', [ReceiptController::class, 'store'])
    ->name('receipts.store');

Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');


Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');

Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
Route::post('/stores', [StoreController::class, 'store']);
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::delete('/stores/{store}', [StoreController::class, 'destroy'])
    ->name('stores.destroy');



Route::post('/receipts/{receipt}/items', [ReceiptItemController::class, 'store']);
Route::post('/receipts/{receipt}/parse', [ReceiptParseController::class, 'parse']);


// JSON
Route::get('/items', [ItemController::class, 'index']);
Route::post('/items', [ItemController::class, 'store']);
