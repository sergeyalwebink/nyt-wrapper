<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BestSellersController;

Route::prefix('v1')->group(function () {
    Route::post('bestsellers/history', [BestSellersController::class, 'history']);
});
