<?php

namespace App\Controllers;

use App\Models\ReceitaModel;
use App\Models\TagModel;

class ReceitaController extends BaseController
{
    public function create()
    {
        $tagModel = new TagModel();

        $dados['tags'] = $tagModel->findAll();

        return view('receita/cadastrar', $dados);
    }

    public function store()
    {
    $receitaModel = new ReceitaModel();

    $dados = [
        'titulo' => $this->request->getPost('titulo'),
        'legenda' => $this->request->getPost('legenda'),
        'tags' => json_encode($this->request->getPost('tags')),
        'data' => date('Y-m-d'),
        'idUsuario' => 1,
        'status' => 'Ativa'
    ];

    $receitaModel->insert($dados);

    return redirect()->to('/receita/cadastrar');
    }
}