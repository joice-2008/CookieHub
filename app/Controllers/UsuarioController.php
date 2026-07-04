<?php

namespace App\Controllers;
use App\Models\UsuarioModel;
class UsuarioController extends BaseController{

    public function cadastrar(){
    $nomeCompleto = $this->request->getPost('nome');
    $login = $this->request->getPost('usuario');
    $senha = $this->request->getPost('senha');
    $modelUsuario = new UsuarioModel();

    if(!empty($nomeCompleto) && !empty($login) && !empty($senha)){
        $dados = ['login' => $login,'nomeCompleto' => $nomeCompleto, 'senha' => password_hash($senha, PASSWORD_DEFAULT)];
            $modelUsuario->insert($dados);
            echo "preenchido.";
        }else{
            echo "preencha todos os campos";
        }
    }

    public function logar(){
        $login = $this->request->getPost('usuario');
        $senha = $this->request->getPost('senha');

        if(!empty($login) && !empty($senha)){
                echo "preenchido.";
        }else{
            echo "preencha todos os campos";
        }
    }
}
?>