// File: ./scripts/update_currencies.php
<?php

//error_log('Запуск обновления данных в фоне...');

define('APP_PATH', dirname(__DIR__));
require_once APP_PATH.'/vendor/autoload.php';

use App\Kernel\Cache\FileCache;
use App\Kernel\Config\Config;
use App\Kernel\Database\Database;
use App\Services\ExchangeRateService;

try {
    $config = new Config();
    $db = new Database($config);
    $cache = new FileCache(APP_PATH.'/cache');

//    error_log('Обновление данных запущено...');
    $exchangeRateService = new ExchangeRateService($db, $cache);

//    error_log('Проверка наличия валют...');
    $exchangeRateService->checkAndLoadCurrenciesIfEmpty();

//    error_log('Попытка обновить данные валют...');
    $exchangeRateService->updateAllCurrencyRatesIfNeeded();

//    error_log('Данные успешно обновлены');
} catch (Exception $e) {
    error_log('Ошибка обновления данных: '.$e->getMessage());
}
