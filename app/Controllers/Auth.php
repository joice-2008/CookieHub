<?php
namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        return view('login');
    }

    public function cadastro()
    {
        return view('cadastro');
    }
}

?>