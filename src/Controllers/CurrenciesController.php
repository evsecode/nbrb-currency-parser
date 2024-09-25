<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\Http\Redirect;
use App\Kernel\Validator\Validator;

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
//        $data = ['currencies' => $this->request()->input('currencies')];
//        $rules = [
//            'currencies' => ['min_checkbox:2'], // минимум 2 чекбокса должны быть выбраны
//        ];
//        $validator = new Validator();
//
//        dd($validator->validate($data, $rules), $validator->errors());
//
//        dd($this->request()->input('currencies'));

        $validation = $this->request()->validate([
            'currencies' => ['min_checkbox:2'],
        ]);

        if (!$validation) {
            foreach ($this->request()->errors() as $field => $errors) {
                $this->session()->set($field, $errors);
            }

            $this->redirect('/admin/currencies/add');
        }

        dd('Validation passed');
    }

    public function exchange(): void
    {
        $this->view('exchange');
    }
}