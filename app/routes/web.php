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

Route::get('/{any}', fn () => view('app'))
    ->where('any', '.*');
