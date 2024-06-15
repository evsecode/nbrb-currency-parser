<?php

use App\Controllers\CurrenciesController;
use App\Controllers\HomeController;
use App\Kernel\Router\Route;

return [
    Route::get('/home', [HomeController::class, 'index']),
    Route::get('/currencies', [CurrenciesController::class, 'index']),
];
