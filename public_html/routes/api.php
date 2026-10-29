<?php

use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoints requeridos por la prueba técnica (y soporte de divisas).
| Disponibles tanto en /products como en /api/products.
|
*/

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show'])->whereNumber('id');
    Route::put('/{id}', [ProductController::class, 'update'])->whereNumber('id');
    Route::patch('/{id}', [ProductController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->whereNumber('id');

    Route::get('/{id}/prices', [ProductPriceController::class, 'index'])->whereNumber('id');
    Route::post('/{id}/prices', [ProductPriceController::class, 'store'])->whereNumber('id');
});

Route::prefix('currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index']);
    Route::post('/', [CurrencyController::class, 'store']);
    Route::get('/{id}', [CurrencyController::class, 'show'])->whereNumber('id');
    Route::put('/{id}', [CurrencyController::class, 'update'])->whereNumber('id');
    Route::patch('/{id}', [CurrencyController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [CurrencyController::class, 'destroy'])->whereNumber('id');
});
