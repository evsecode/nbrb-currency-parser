<?php

namespace App\Services;

class NBRBApiClient
{
    private const BASE_URL = 'https://www.nbrb.by/api/exrates/';

    public function fetchCurrencies(): array
    {
        return $this->fetchJson(self::BASE_URL.'currencies');
    }

    public function fetchHistoricalRates(int $currencyId, string $startDate, string $endDate): array
    {
        $url = self::BASE_URL."rates/dynamics/{$currencyId}?startDate={$startDate}&endDate={$endDate}";

        try {
            $data = $this->fetchJson($url);

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function fetchJson(string $url): array
    {
        $response = file_get_contents($url);
        if ($response === false) {
            throw new \Exception("Error fetching data from {$url}");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error decoding JSON response.');
        }

        return $data;
    }
}
