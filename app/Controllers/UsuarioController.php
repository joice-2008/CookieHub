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
            $usuarioRepetido = $modelUsuario->where('login', $login)->first();
            if(!$usuarioRepetido){
                $dados = ['login' => $login,'nomeCompleto' => $nomeCompleto, 'senha' => password_hash($senha, PASSWORD_DEFAULT)];
                $modelUsuario->insert($dados);
                session()->set('usuarioCadastrado', true);
                return redirect()->to('/login');
            }else{
                session()->setFlashdata('nomeRepetido', 'O nome de usuário já foi registrado em outro cadastro.');
                return redirect()->to('/cadastro');
            }
        }else{
            session()->setFlashdata('campoVazio', 'Preencha todos os campos.');
            return redirect()->to('/cadastro');;
        }
    }

    public function logar(){
        $login = $this->request->getPost('usuario');
        $senha = $this->request->getPost('senha');
        $modelUsuario = new UsuarioModel();
        $usuario = $modelUsuario->where('login', $login)->first();

        if(!empty($login) && !empty($senha)){
            if($usuario && password_verify($senha, $usuario['senha'])){
                session()->set('usuarioLogado', true);
                return redirect()->to('/entrar');
            }else{
                session()->setFlashdata('loginNaoEncontrado', 'Usuário ou senha incorretos.');
                return redirect()->to('/login');
            }
                
        }else{
            session()->setFlashdata('campoVazio', 'Preencha todos os campos.');
            return redirect()->to('/login');
        }
    }

    public function logout(){
        session()->destroy();
        redirect(base_url('login'));
    }
}
?>