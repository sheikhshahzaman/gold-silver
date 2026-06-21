<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TickerController;
use App\Http\Controllers\Api\VerifyController;
use Illuminate\Support\Facades\Route;

Route::get('/prices', [PriceController::class, 'index'])->middleware('throttle:120,1');

// Mobile app endpoints (public, throttled — same trust model as the website)
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/products', [CatalogController::class, 'products']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/ticker', [TickerController::class, 'index']);
    Route::get('/app-config', [SettingsController::class, 'appConfig']);
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
});

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/buy-sell-orders', [OrderController::class, 'storeMetalOrder']);
    Route::post('/orders/{orderNumber}/payment', [OrderController::class, 'submitPayment']);
    Route::post('/verify', [VerifyController::class, 'verify']);
    Route::post('/contact', [ContactController::class, 'store']);
});
