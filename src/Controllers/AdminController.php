<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;
use App\Services\ExchangeRateService;

class AdminController extends Controller
{
    public function getArchivedCurrencies()
    {
        if (! $this->auth()->user()->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $exchangeService = new ExchangeRateService($this->db(), $this->cache());
        $archivedCurrencies = $exchangeService->getArchivedCurrencies();

        echo json_encode($archivedCurrencies);
    }
//    public function add(): void
//    {
//        $this->view('admin/add');
//    }
//
//    public function store(): void
//    {
//        $validation = $this->request()->validate([
//            'currencies' => ['min_checkbox:2'],
//        ]);
//
//        if (! $validation) {
//            foreach ($this->request()->errors() as $field => $errors) {
//                $this->session()->set($field, $errors);
//            }
//
//            $this->redirect('/admin/add');
//        }
//    }
}
