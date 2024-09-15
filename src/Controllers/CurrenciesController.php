<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;

class CurrenciesController extends Controller
{

    public function index(): void
    {
        $this->view('currencies');
    }

    public function add(): void
    {
        $this->view('admin/currencies/add');
    }

    public function store(): void
    {
        dd($this->request()->input('currencies'));
    }

    public function exchange(): void
    {
        $this->view('exchange');
    }
}