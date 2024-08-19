<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;

class CurrenciesController extends Controller
{

    public function index(): void
    {
        $this->view('currencies');
    }

    public function exchange(): void
    {
        $this->view('exchange');
    }
}