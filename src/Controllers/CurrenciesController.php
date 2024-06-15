<?php

namespace App\Controllers;

class CurrenciesController
{

    public function index(): void
    {
        include_once APP_PATH.'/views/pages/currencies.php';
    }
}