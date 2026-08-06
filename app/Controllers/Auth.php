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

    public function entrar(){
        return redirect()->to('/feed');
    }

    public function logout(){
        return view('index');
    }

    public function perfil(){
        return view('visualizarCadUsuario');
    }

    public function receitaSalva(){
        return view('visualizarCadUsuario');
    }

}


?>