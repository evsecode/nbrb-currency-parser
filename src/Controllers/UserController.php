<?php

namespace App\Controllers;

use App\Kernel\Controller\Controller;

class UserController extends Controller
{
    public function index()
    {
        $user = $this->auth()->user();
        if (! $user) {
            return $this->redirect('/login');
        }

        $this->view('user', [
            'user' => $user,
            'isAdmin' => $user->isAdmin() // Здесь передается isAdmin
        ]);
    }
}