<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;

class RegisterController extends Controller
{
    public function index(): void
    {
        $this->view(name: 'register');
    }

    public function register()
    {
        $validation = $this->request()->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'max:255'],
            'password' => ['required', 'min:8'],
//            'password_confirmation' => ['required', 'min:8'],
        ]);

        if (!$validation) {
            foreach ($this->request()->errors() as $field => $errors) {
                $this->session()->set($field, $errors);
            }

            $this->redirect('/register');
        }

        $userId = $this->db()->insert('users', [
            'email' => $this->request()->input('email'),
            'name' => $this->request()->input('name'),
            'password' => password_hash($this->request()->input('password'), PASSWORD_DEFAULT),
        ]);

        $this->redirect('/');
    }
}