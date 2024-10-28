<?php

namespace App\Services;

use App\Kernel\Cache\CacheInterface;
use App\Kernel\Database\Database;
use App\Models\ExchangeRate;
use Exception;

class ExchangeRateService
{
    private Database $db;

    private NBRBApiClient $apiClient;

    private CacheInterface $cache;

    private const CACHE_KEY = 'last_update';

    private const UPDATE_INTERVAL = 86400;

    public function __construct(Database $db, CacheInterface $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->apiClient = new NBRBApiClient();
    }

    public function checkAndUpdateIfNeeded(): void
    {
        $this->checkAndLoadCurrenciesIfEmpty();

        $lastUpdate = $this->cache->get(self::CACHE_KEY);

        if (!$lastUpdate || (time() - $lastUpdate) > self::UPDATE_INTERVAL) {
            error_log('Кеш устарел или отсутствует. Запускаем обновление данных...');
            $this->runAsyncUpdate();

            $currentDay = strtotime(date('Y-m-d'));
            error_log("Попытка установить новый кеш со временем: $currentDay");
            $this->cache->set(self::CACHE_KEY, $currentDay, self::UPDATE_INTERVAL);
        } else {
            error_log('Данные актуальны. Кеш обновлять не нужно.');
        }
    }


    private function runAsyncUpdate(): void
    {
        error_log('Запуск фонового процесса обновления данных...');

        $command = 'php ' . APP_PATH . '/scripts/update_currencies.php > /dev/null 2>&1 &';
        exec($command);
    }

    public function updateAllCurrencyRatesIfNeeded(): void
    {
        $currencies = $this->fetchAllCurrencies();
        $this->saveCurrenciesToDB($currencies);

        $endDate = date('Y-m-d');
        foreach ($currencies as $currency) {
            $currencyId = $currency['Cur_ID'];

            if (!empty($currency['last_update'])) {
                $lastUpdatedDate = $currency['last_update'];
                error_log("Последняя дата обновления для валюты $currencyId: $lastUpdatedDate");
            } else {
                $lastUpdatedDate = $currency['Cur_DateStart'];
                error_log("last_update для валюты $currencyId не найдено. Используем Cur_DateStart: $lastUpdatedDate");
            }

            if (strtotime($lastUpdatedDate . ' +1 day') === strtotime($endDate)) {
                $startDate = date('Y-m-d', strtotime($lastUpdatedDate));
            } else {
                $startDate = date('Y-m-d', strtotime($lastUpdatedDate . ' +1 day'));
            }

            if ($startDate <= $endDate) {
                error_log("Попытка загрузить курсы валют для $currencyId с $startDate по $endDate");
                $rates = $this->fetchHistoricalRates($currencyId, $startDate, $endDate);

                if (!empty($rates)) {
                    $this->saveCurrencyRatesToDB($currency, $rates);

                    $this->db->query('UPDATE currencies SET last_update = ? WHERE id = ?', [$endDate, $currencyId]);
                    error_log("Курсы для валюты $currencyId обновлены и сохранены.");
                } else {
                    error_log("Нет новых данных для валюты $currencyId.");
                }
            } else {
                error_log("Данные для валюты $currencyId уже актуальны.");
            }
        }
    }

    private function fetchAllCurrencies(): array
    {
        $currencies = $this->db->get('currencies', []);

        return array_map(function ($currency) {
            return [
                'Cur_ID' => $currency['id'],
                'Cur_Abbreviation' => $currency['abbreviation'],
                'Cur_Name' => $currency['name'],
                'Cur_Scale' => $currency['scale'],
                'Cur_DateStart' => $currency['start_date'],
                'Cur_DateEnd' => $currency['end_date'],
                'last_update' => $currency['last_update'],
            ];
        }, $currencies);
    }

    public function fetchHistoricalRates(int $currencyId, string $startDate, string $endDate): array
    {
        $allRates = [];
        $currentStartDate = $startDate;

        while ($currentStartDate < $endDate) {
            $nextYear = date('Y-m-d', strtotime('+1 year', strtotime($currentStartDate)));
            if ($nextYear > $endDate) {
                $nextYear = $endDate;
            }

            $rates = $this->apiClient->fetchHistoricalRates($currencyId, $currentStartDate, $nextYear);
            if (!empty($rates)) {
                $allRates = array_merge($allRates, $rates);
            }

            $currentStartDate = $nextYear;
        }

        return $allRates;
    }

    private function saveCurrenciesToDB(array $currencies): void
    {
        foreach ($currencies as $currency) {
            $existingCurrency = $this->db->first('currencies', ['id' => $currency['Cur_ID']]);
            if (!$existingCurrency) {
                $this->db->insert('currencies', [
                    'id' => $currency['Cur_ID'],
                    'abbreviation' => $currency['Cur_Abbreviation'],
                    'name' => $currency['Cur_Name_Eng'],
                    'scale' => $currency['Cur_Scale'],
                    'start_date' => $currency['Cur_DateStart'],
                    'end_date' => $currency['Cur_DateEnd'],
                ]);
            }
        }
    }

    private function saveCurrencyRatesToDB(array $currency, array $rates): void
    {
        foreach ($rates as $rateData) {
            $existingRate = $this->db->first('historical_rates', [
                'currency_id' => $currency['Cur_ID'],
                'rate_date' => substr($rateData['Date'], 0, 10),
            ]);

            if (!$existingRate) {
                $this->db->insert('historical_rates', [
                    'currency_id' => $currency['Cur_ID'],
                    'rate' => $rateData['Cur_OfficialRate'],
                    'rate_date' => substr($rateData['Date'], 0, 10),
                    'updated_at' => date('Y-m-d'),
                ]);
            }
        }
    }

    private function getLastUpdatedDate(int $currencyId): ?string
    {
        $result = $this->db->first('historical_rates', ['currency_id' => $currencyId], 'rate_date DESC');

        return $result['rate_date'] ?? null;
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): array
    {
        $currentDate = date('Y-m-d');

        if ($fromCurrency === 'BYN') {
            $fromRate = ['rate' => 1.00, 'scale' => 1];
        } else {
            $from = $this->db->first('currencies', [
                'abbreviation' => $fromCurrency,
                'start_date <= ? AND end_date >= ?' => [$currentDate, $currentDate],
            ]);

            if (!$from) {
                throw new Exception("Валюта отправления $fromCurrency не найдена.");
            }

            $fromRate = $this->db->first('historical_rates', [
                'currency_id' => $from['id'],
                'rate_date <= ?' => [$currentDate],
            ], 'rate_date DESC');

            if (!$fromRate) {
                throw new Exception("Курс валюты отправления $fromCurrency не найден.");
            }

            $fromRate['scale'] = $from['scale'];
        }

        if ($toCurrency === 'BYN') {
            $toRate = ['rate' => 1.00, 'scale' => 1];
        } else {
            $to = $this->db->first('currencies', [
                'abbreviation' => $toCurrency,
                'start_date <= ? AND end_date >= ?' => [$currentDate, $currentDate],
            ]);

            if (!$to) {
                throw new Exception("Валюта назначения $toCurrency не найдена.");
            }

            $toRate = $this->db->first('historical_rates', [
                'currency_id' => $to['id'],
                'rate_date <= ?' => [$currentDate],
            ], 'rate_date DESC');

            if (!$toRate) {
                throw new Exception("Курс валюты назначения $toCurrency не найден.");
            }

            $toRate['scale'] = $to['scale'];
        }

        $fromScale = $fromRate['scale'];
        $toScale = $toRate['scale'];
        $exchangeRate = ($fromRate['rate'] / $fromScale) / ($toRate['rate'] / $toScale);
        $reverseRate = ($toRate['rate'] / $toScale) / ($fromRate['rate'] / $fromScale);

        return [
            'fromScale' => $fromScale,
            'toScale' => $toScale,
            'exchangeRate' => $exchangeRate,
            'reverseRate' => $reverseRate,
        ];
    }

    public function getRatesByDate(string $date): array
    {
        $query = '
            SELECT her.currency_id, c.abbreviation, c.name, c.scale, her.rate
            FROM historical_rates her
            JOIN currencies c ON her.currency_id = c.id
            WHERE her.rate_date = :date
        ';

        $data = $this->db->query($query, ['date' => $date]);

        return array_map(function ($rate) {
            return new ExchangeRate(
                Cur_ID: $rate['currency_id'],
                Cur_Abbreviation: $rate['abbreviation'],
                Cur_Name: $rate['name'],
                Cur_Scale: $rate['scale'],
                Cur_OfficialRate: $rate['rate']
            );
        }, $data);
    }

    public function getAllCurrencies(): array
    {
        $currentDate = date('Y-m-d');
        $currencies = $this->db->get('currencies', [
            'start_date <= ? AND end_date >= ?' => [$currentDate, $currentDate],
        ]);

        return array_map(function ($currency) {
            return [
                'id' => $currency['id'],
                'abbreviation' => $currency['abbreviation'],
                'name' => $currency['name'],
                'scale' => $currency['scale'],
            ];
        }, $currencies);
    }

    public function getHistoricalRatesForGraph(int $currencyId, string $startDate, string $endDate): array
    {
        return $this->db->query(
            'SELECT rate, rate_date FROM historical_rates WHERE currency_id = ? AND rate_date BETWEEN ? AND ? ORDER BY rate_date ASC',
            [$currencyId, $startDate, $endDate]
        );
    }
    public function getCurrentCurrencyIdByAbbreviation(string $currencyAbbr, string $currentDate): ?int
    {
        $currency = $this->db->first('currencies', [
            'abbreviation' => $currencyAbbr,
            'start_date <= ? AND end_date >= ?' => [$currentDate, $currentDate],
        ]);

        return $currency['id'] ?? null;
    }

    public function checkAndLoadCurrenciesIfEmpty(): void
    {
        $currencies = $this->db->get('currencies', []);

        if (empty($currencies)) {
            error_log('Таблица currencies пуста. Выполняем начальную загрузку...');
            $this->loadCurrenciesFromApi();
        }
    }

    private function loadCurrenciesFromApi(): void
    {
        $apiCurrencies = $this->apiClient->fetchCurrencies();

        if (!empty($apiCurrencies)) {
            $this->saveCurrenciesToDB($apiCurrencies);
            error_log('Валюты успешно загружены в базу данных.');
        } else {
            error_log('Ошибка: Не удалось получить валюты из API.');
            throw new \Exception('Ошибка получения валют из API.');
        }
    }
    public function getUniqueRatesByDate(string $date): array
    {
        $query = '
        SELECT DISTINCT her.currency_id, c.abbreviation, c.name, c.scale, her.rate
        FROM historical_rates her
        JOIN currencies c ON her.currency_id = c.id
        WHERE her.rate_date = :date
    ';

        $data = $this->db->query($query, ['date' => $date]);

        return array_map(function ($rate) {
            return new ExchangeRate(
                Cur_ID: $rate['currency_id'],
                Cur_Abbreviation: $rate['abbreviation'],
                Cur_Name: $rate['name'],
                Cur_Scale: $rate['scale'],
                Cur_OfficialRate: $rate['rate']
            );
        }, $data);
    }

    public function getArchivedCurrencies(): array
    {
        $query = 'SELECT abbreviation, name, start_date, end_date FROM currencies WHERE end_date < NOW()';
        return $this->db->query($query);
    }
}
