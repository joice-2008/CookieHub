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


    public function salvar(){
        dd($this->request->getPost());
        if(!session()->get('usuarioLogado')){
            return redirect()->to('/login');
        }
        $model = new \App\Models\ReceitaModel();  

        if(!session()->get('usuarioLogado')){
            return redirect()->to('/login');
        }

        $titulo = $this->request->getPost("titulo");
        $legenda = $this->request->getPost("legenda");
        $tags = $this->request->getPost("tags");
        $ingredientes = $this->request->getPost("ingredientes");
        $imagem = $this->request->getFile('imagem');  

        if(empty($titulo)){
            return redirect()->back()->with("erro","Informe o título.");
        }

        if(empty($ingredientes)){
            return redirect()->back()->with("erro","Adicione pelo menos um ingrediente.");
        }

        if(empty($tags)){
            return redirect()->back()->with("erro","Selecione pelo menos uma tag.");
        }

        $nomeImagem = "";
        if($imagem->isValid() && !$imagem->hasMoved()){
            $nomeImagem = $imagem->getRandomName();
            $imagem->move(ROOTPATH . 'public/uploads',$nomeImagem);
        }

        $tagsJson = json_encode($tags);

        $dadosIngredientes = $this->prepararIngredientes($ingredientes);

        $idUsuario = session()->get('idUsuario');

        $dadosReceita = [
            'titulo' => $titulo,
            'legenda' => $legenda,
            'imagem' => $nomeImagem,
            'tags' => json_encode($tags),
            'ingredientes' => $dadosIngredientes["ingredientes"],
            'quantidadeIngredientes' => $dadosIngredientes["quantidades"],
            'infosNutricionais' => $dadosIngredientes["infos"],
            'data' => date("Y-m-d"),
            'idUsuario' => $idUsuario,
            'status' => "Ativa"
        ];

        if($model->insert($dadosReceita)){
            return redirect()->to(base_url('receitaSalva'))->with('sucesso', 'Receita cadastrada com sucesso!');
        }
        return redirect()->back()->withInput()->with('erro', 'Erro ao cadastrar a receita.');

    }

    private function prepararIngredientes(array $ingredientes){
        $listaIngredientes = [];
        $listaQuantidades = [];
        $infosNutricionais = [];

        $totalCalorias = 0;
        $totalProteinas = 0;
        $totalCarboidratos = 0;
        $totalGorduras = 0;

        foreach($ingredientes as $ingrediente){
            $listaIngredientes[] = $ingrediente['nome'];
            $listaQuantidades[] = $ingrediente['quantidade'];

            $fator = $ingrediente["quantidade"] / 100;

            $calorias = round($ingrediente['calorias'] * $fator, 2);
            $proteinas = round($ingrediente['proteinas'] * $fator, 2);
            $carboidratos = round($ingrediente['carboidratos'] * $fator, 2);
            $gorduras = round($ingrediente['gorduras'] * $fator, 2);

            $totalCalorias += $calorias;
            $totalProteinas += $proteinas;
            $totalCarboidratos += $carboidratos;
            $totalGorduras += $gorduras;

            $infosNutricionais[] = [
                'idApi' => $ingrediente['idApi'],
                'nome' => $ingrediente['nome'],
                'quantidade' => $ingrediente['quantidade'],
                'calorias' => $calorias,
                'proteinas' => $proteinas,
                'carboidratos' => $carboidratos,
                'gorduras' => $gorduras
            ];
        }

        $ingredientesJson = json_encode($listaIngredientes);
        $quantidadesJson = json_encode($listaQuantidades);
        $infosJson = json_encode([
            'total' => [
                'calorias' => round($totalCalorias, 2),
                'proteinas' => round($totalProteinas, 2),
                'carboidratos' => round($totalCarboidratos, 2),
                'gorduras' => round($totalGorduras, 2)
            ],
            'ingredientes' => $infosNutricionais
        ]);

       return ["ingredientes" => $ingredientesJson, "quantidades" => $quantidadesJson, "infos" => $infosJson]; 

    }

    public function listar(){
        $receitaModel = new ReceitaModel();
        $tagModel = new TagModel();

        $receitas = $receitaModel
            ->select('receita.*, usuario.nomeCompleto AS nomeUsuario')
            ->join('usuario', 'usuario.idUsuario = receita.idUsuario')
            ->orderBy('receita.idReceita', 'DESC')
            ->findAll();

        foreach ($receitas as &$receita) {

            $idsTags = json_decode($receita['tags'], true);

            if (!empty($idsTags)) {
                $tags = $tagModel
                    ->whereIn('idTag', $idsTags)
                    ->findAll();

                $receita['nomesTags'] = array_column($tags, 'nome');
            } else {
                $receita['nomesTags'] = [];
            }
        }

        return view('feed', [
            'receitas' => $receitas
        ]);
    }

    public function listarReceitaUsuario(){
        $receitaModel = new ReceitaModel();
        $tagModel = new TagModel();

        $idUsuario = session()->get('idUsuario');

        $receitas = $receitaModel
            ->select('receita.*, usuario.nomeCompleto AS nomeUsuario')
            ->join('usuario', 'usuario.idUsuario = receita.idUsuario')
            ->where('receita.idUsuario', $idUsuario)
            ->orderBy('receita.idReceita', 'DESC')
            ->findAll();


        foreach ($receitas as &$receita) {

            $idsTags = json_decode($receita['tags'], true);

            if (!empty($idsTags)) {

                $tags = $tagModel
                    ->whereIn('idTag', $idsTags)
                    ->findAll();

                $receita['nomesTags'] = array_column($tags, 'nome');

            } else {

                $receita['nomesTags'] = [];

            }
        }

        return view('visualizarCadUsuario', [
            'receitas' => $receitas,    
            'nomeUsuario' => session()->get('nomeUsuario'),
            'loginUsuario' => session()->get('loginUsuario')
        ]);
    }
    public function visualizarReceita($idReceita){
    $receitaModel = new ReceitaModel();
    $tagModel = new TagModel();

    $receita = $receitaModel
        ->select('receita.*, usuario.nomeCompleto AS nomeUsuario')
        ->join('usuario', 'usuario.idUsuario = receita.idUsuario')
        ->where('receita.idReceita', $idReceita)
        ->first();

    if (!$receita) {
        return redirect()->to(base_url('receita'));
    }

    $idsTags = json_decode($receita['tags'] ?? '', true);

    if (!is_array($idsTags)) {$idsTags = [];}

    if (!empty($idsTags)) {

        $tags = $tagModel
            ->whereIn('idTag', $idsTags)
            ->findAll();

        $receita['nomesTags'] = array_column($tags, 'nome');

    } else {

        $receita['nomesTags'] = [];

    }

    $receita['ingredientes'] = json_decode($receita['ingredientes'] ?? '',true);

    if (!is_array($receita['ingredientes'])) {$receita['ingredientes'] = [];}

    $receita['quantidadeIngredientes'] = json_decode($receita['quantidadeIngredientes'] ?? '',true);

    if (!is_array($receita['quantidadeIngredientes'])) {$receita['quantidadeIngredientes'] = [];}

    $receita['infosNutricionais'] = json_decode($receita['infosNutricionais'] ?? '',true);

    if (!is_array($receita['infosNutricionais'])) {$receita['infosNutricionais'] = [];}

        $totalCalorias = 0;
        $totalProteinas = 0;
        $totalCarboidratos = 0;
        $totalGorduras = 0;

        if (is_array($receita['infosNutricionais'])) {

            foreach ($receita['infosNutricionais'] as $info) {

                if (!is_array($info)) {
                    continue;
                }

                $totalCalorias += (float) ($info['calorias'] ?? 0);
                $totalProteinas += (float) ($info['proteinas'] ?? 0);
                $totalCarboidratos += (float) ($info['carboidratos'] ?? 0);
                $totalGorduras += (float) ($info['gorduras'] ?? 0);

            }

        }

    return view('visualizarReceita', [
        'receita' => $receita,
        'totalCalorias' => $totalCalorias,
        'totalProteinas' => $totalProteinas,
        'totalCarboidratos' => $totalCarboidratos,
        'totalGorduras' => $totalGorduras
    ]);
    }

    public function editar($idReceita)
    {
        dd($idReceita);

        $receitaModel = new ReceitaModel();
        $tagModel = new TagModel();

        $receita = $receitaModel
            ->where('idReceita', $idReceita)
            ->first();

        if (!$receita) {
            return redirect()->back();
        }

        // Verifica se a receita pertence ao usuário logado
        if ($receita['idUsuario'] != session()->get('idUsuario')) {
            return redirect()->back();
        }

        // Ingredientes
        $receita['ingredientes'] = json_decode(
            $receita['ingredientes'] ?? '',
            true
        );

        if (!is_array($receita['ingredientes'])) {
            $receita['ingredientes'] = [];
        }

        // Quantidades
        $receita['quantidadeIngredientes'] = json_decode(
            $receita['quantidadeIngredientes'] ?? '',
            true
        );

        if (!is_array($receita['quantidadeIngredientes'])) {
            $receita['quantidadeIngredientes'] = [];
        }

        // Tags
        $idsTags = json_decode(
            $receita['tags'] ?? '',
            true
        );

        if (!is_array($idsTags)) {
            $idsTags = [];
        }

        $tags = $tagModel->findAll();

        

        return view('editarReceita', [
            'receita' => $receita,
            'tags' => $tags,
            'idsTags' => $idsTags
        ]);
    }
}