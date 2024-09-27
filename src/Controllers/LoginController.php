<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;

class LoginController extends Controller
{
    public function index(): void
    {
        $this->view(name: 'login');
    }

    public function login(): void
    {
        $email = $this->request()->input('email');
        $password = $this->request()->input('password');
        $this->auth()->attempt($email, $password);

        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->auth()->logout();
        $this->redirect('/login');
    }
}
