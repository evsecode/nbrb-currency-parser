<?php

namespace App\Controllers;

use App\Services\ExchangeRateService;

class CurrenciesController extends BaseController
{
    public function index(): void
    {
        $this->checkCurrencyData();

        $exchangeService = new ExchangeRateService($this->db(), $this->cache());
        $currencies = $exchangeService->getAllCurrencies();

        $this->view('currencies', [
            'currencies' => $currencies,
        ]);
    }

    public function convert(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        try {
            $inputData = json_decode(file_get_contents('php://input'), true);

            $fromCurrency = $inputData['from_currency'] ?? 'BYN';
            $toCurrency = $inputData['to_currency'] ?? 'USD';
            $amount = (float) ($inputData['amount'] ?? 1);

            error_log("Запрос на конвертацию: from $fromCurrency, to $toCurrency, amount $amount");

            $exchangeService = new \App\Services\ExchangeRateService($this->db(), $this->cache());

            $conversionRateInfo = $exchangeService->getExchangeRate($fromCurrency, $toCurrency);

            if (! $conversionRateInfo) {
                throw new \Exception("Не удалось получить курс обмена для $fromCurrency -> $toCurrency.");
            }

            $fromScale = $conversionRateInfo['fromScale'];
            $toScale = $conversionRateInfo['toScale'];
            $exchangeRate = $conversionRateInfo['exchangeRate'];
            $reverseRate = $conversionRateInfo['reverseRate'];

            $result = $exchangeRate * $amount;

            error_log("Результат конвертации: $result");

            echo json_encode([
                'rate' => $exchangeRate,
                'reverseRate' => $reverseRate,
                'result' => $result,
                'fromCurrency' => $fromCurrency,
                'toCurrency' => $toCurrency,
                'amount' => $amount,
                'fromScale' => $fromScale,
                'toScale' => $toScale,
            ]);

        } catch (\Exception $e) {
            error_log('Ошибка конвертации: '.$e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function getHistoricalRates(): void
    {
        header('Content-Type: application/json');
        $currencyAbbr = $_GET['currency_abbr'] ?? null;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $exchangeService = new ExchangeRateService($this->db(), $this->cache());

        if (!$currencyAbbr || !$startDate || !$endDate) {
            echo json_encode(['error' => 'Invalid parameters']);
            return;
        }

        $currentDate = date('Y-m-d');
        $currencyId = $exchangeService->getCurrentCurrencyIdByAbbreviation($currencyAbbr, $currentDate);

        if (!$currencyId) {
            echo json_encode(['error' => 'Currency not found or inactive']);
            return;
        }

        $historicalRates = $exchangeService->fetchHistoricalRates($currencyId, $startDate, $endDate);
        echo json_encode($historicalRates);
    }
}
