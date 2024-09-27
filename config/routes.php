<?php

use App\Controllers\CurrenciesController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Kernel\Router\Route;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

return [
    Route::get('/', [CurrenciesController::class, 'index']),
    Route::get('/currencies', [CurrenciesController::class, 'index']),
    Route::get('/admin/currencies/add', [CurrenciesController::class, 'add', [AuthMiddleware::class]]),
    Route::post('/admin/currencies/add', [CurrenciesController::class, 'store']),
    Route::get('/exchange', [CurrenciesController::class, 'exchange']),
    Route::get('/register', [RegisterController::class, 'index'], [GuestMiddleware::class]),
    Route::get('/login', [LoginController::class, 'index'], [GuestMiddleware::class]),
    Route::post('/register', [RegisterController::class, 'register']),
    Route::post('/login', [LoginController::class, 'login']),
    Route::post('/logout', [LoginController::class, 'logout']),
];
