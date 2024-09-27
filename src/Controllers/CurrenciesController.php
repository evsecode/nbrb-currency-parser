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
        $validation = $this->request()->validate([
            'currencies' => ['min_checkbox:2'],
        ]);

        if (! $validation) {
            foreach ($this->request()->errors() as $field => $errors) {
                $this->session()->set($field, $errors);
            }

            $this->redirect('/admin/currencies/add');
        }
    }

    public function exchange(): void
    {
        $this->view('exchange');
    }
}
