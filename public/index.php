<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/error.log');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
//echo 'Log location: '.ini_get('error_log');

define('APP_PATH', dirname(__DIR__));

require_once APP_PATH.'/vendor/autoload.php';

use App\Kernel\App;

$app = new App();

$app->run();
