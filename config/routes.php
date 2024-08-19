<?php

use App\Controllers\CurrenciesController;
use App\Controllers\HomeController;
use App\Kernel\Router\Route;

return [
    Route::get('/', [CurrenciesController::class, 'index']),
    Route::get('/currencies', [CurrenciesController::class, 'index']),
    Route::get('/exchange', [CurrenciesController::class, 'exchange']),
];
