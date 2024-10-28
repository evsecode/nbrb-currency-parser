<?php

namespace App\Controllers;

use App\Services\ExchangeRateService;

class ExchangeController extends BaseController
{
    public function index(): void
    {
        $exchangeService = new ExchangeRateService($this->db(), $this->cache());
        $date = $this->request()->input('date') ?? date('Y-m-d');

        $exchangeRates = $exchangeService->getUniqueRatesByDate($date);

        if ($this->request()->isAjax()) {
            $this->renderAjaxTable($exchangeRates);
        } else {
            $this->view('exchange', [
                'exchangeRates' => $exchangeRates,
            ]);
        }
    }
    private function renderAjaxTable(array $exchangeRates): void
    {
        foreach ($exchangeRates as $rate) {
            echo "<tr>
                <td>{$rate->Cur_Abbreviation()}</td>
                <td>{$rate->Cur_Name()}</td>
                <td>{$rate->Cur_Scale()}</td>
                <td>{$rate->Cur_OfficialRate()}</td>
            </tr>";
        }
    }
}
