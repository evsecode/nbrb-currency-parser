<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;
use App\Services\ExchangeRateService;

abstract class BaseController extends Controller
{
    protected function checkCurrencyData(): void
    {
        $exchangeService = new ExchangeRateService($this->db(), $this->cache());

        $exchangeService->checkAndUpdateIfNeeded();
    }
}
