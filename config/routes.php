<?php

use App\Controllers\AdminController;
use App\Controllers\CurrenciesController;
use App\Controllers\ExchangeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\UserController;
use App\Kernel\Router\Route;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

return [
    Route::get('/', [CurrenciesController::class, 'index']),
    Route::get('/currencies', [CurrenciesController::class, 'index']),
    Route::get('/exchange', [ExchangeController::class, 'index']),
    Route::post('/convert', [CurrenciesController::class, 'convert']),
    Route::get('/currencies/historical', [CurrenciesController::class, 'getHistoricalRates']),
    Route::get('/user', [UserController::class, 'index']),
//    Route::get('/admin/add', [AdminController::class, 'add', [AuthMiddleware::class]]),
//    Route::post('/admin/add', [AdminController::class, 'store']),
    Route::get('/admin/archive-currencies', [AdminController::class, 'getArchivedCurrencies']),
    Route::get('/register', [RegisterController::class, 'index'], [GuestMiddleware::class]),
    Route::get('/login', [LoginController::class, 'index'], [GuestMiddleware::class]),
    Route::post('/register', [RegisterController::class, 'register']),
    Route::post('/login', [LoginController::class, 'login']),
    Route::post('/logout', [LoginController::class, 'logout']),
];
