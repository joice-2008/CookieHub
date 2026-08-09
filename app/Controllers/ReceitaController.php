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
        if(!session()->get('usuarioLogado')){
            return redirect()->to('/login');
        }
        $model = new \App\Models\ReceitaModel();  

        

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
            return redirect()->back()->with("erro","Selecione pelo menos uma categoria.");
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
            $idReceita = $model->getInsertID();

        return redirect()
            ->to(base_url('receita/visualizar/' . $idReceita))
            ->with('sucesso', 'Receita cadastrada com sucesso!');
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
        $receitaModel = new ReceitaModel();
        $tagModel = new TagModel();

        $receita = $receitaModel
            ->where('idReceita', $idReceita)
            ->first();

        if (!$receita) {
            return redirect()->back();
        }

        if ($receita['idUsuario'] != session()->get('idUsuario')) {
            return redirect()->back();
        }

        $receita['ingredientes'] = json_decode(
            $receita['ingredientes'] ?? '',
            true
        );

        if (!is_array($receita['ingredientes'])) {
            $receita['ingredientes'] = [];
        }

        $receita['quantidadeIngredientes'] = json_decode(
            $receita['quantidadeIngredientes'] ?? '',
            true
        );

        if (!is_array($receita['quantidadeIngredientes'])) {
            $receita['quantidadeIngredientes'] = [];
        }

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

    public function atualizar($idReceita)
{
    if (!session()->get('usuarioLogado')) {
        return redirect()->to('/login');
    }

    $receitaModel = new ReceitaModel();

    // Busca a receita
    $receita = $receitaModel->find($idReceita);

    if (!$receita) {
        return redirect()->back()
            ->with('erro', 'Receita não encontrada.');
    }

    // Verifica se a receita pertence ao usuário logado
    if ($receita['idUsuario'] != session()->get('idUsuario')) {
        return redirect()->back()
            ->with('erro', 'Você não pode editar esta receita.');
    }

    // Dados enviados pelo formulário
    $titulo = $this->request->getPost('titulo');
    $legenda = $this->request->getPost('legenda');
    $tags = $this->request->getPost('tags') ?? [];
    $ingredientes = $this->request->getPost('ingredientes') ?? [];


    // Validações
    if (empty($titulo)) {
        return redirect()->back()
            ->withInput()
            ->with('erro', 'Informe o título.');
    }

    if (empty($ingredientes)) {
        return redirect()->back()
            ->withInput()
            ->with('erro', 'Adicione pelo menos um ingrediente.');
    }

    if (empty($tags)) {
        return redirect()->back()
            ->withInput()
            ->with('erro', 'Selecione pelo menos uma tag.');
    }


    /*
     * ==========================================================
     * RECUPERA OS DADOS NUTRICIONAIS ANTIGOS
     * ==========================================================
     */

    $infosAntigas = json_decode(
        $receita['infosNutricionais'] ?? '{}',
        true
    );

    if (!is_array($infosAntigas)) {
        $infosAntigas = [];
    }

    // Os ingredientes antigos ficam dentro de "ingredientes"
    $ingredientesAntigos = $infosAntigas['ingredientes'] ?? [];

    if (!is_array($ingredientesAntigos)) {
        $ingredientesAntigos = [];
    }


    /*
     * ==========================================================
     * MONTA OS NOVOS DADOS
     * ==========================================================
     */

    $nomesIngredientes = [];
    $quantidades = [];
    $infosNutricionais = [];


    foreach ($ingredientes as $ingrediente) {

        $nome = $ingrediente['nome'];
        $quantidadeNova = (float) $ingrediente['quantidade'];

        $nomesIngredientes[] = $nome;
        $quantidades[] = $quantidadeNova;


        /*
         * Dados nutricionais enviados pelo formulário.
         */
        $idApi = $ingrediente['idApi'] ?? null;

        $calorias = (float) ($ingrediente['calorias'] ?? 0);
        $proteinas = (float) ($ingrediente['proteinas'] ?? 0);
        $carboidratos = (float) ($ingrediente['carboidratos'] ?? 0);
        $gorduras = (float) ($ingrediente['gorduras'] ?? 0);


        /*
         * Procura o ingrediente antigo pelo nome.
         */
        $ingredienteAntigo = null;

        foreach ($ingredientesAntigos as $antigo) {

            if (
                isset($antigo['nome']) &&
                $antigo['nome'] === $nome
            ) {
                $ingredienteAntigo = $antigo;
                break;
            }
        }


        /*
         * Se o formulário não enviou informações nutricionais,
         * utiliza as informações que já estavam salvas.
         */
        if (
            $calorias == 0 &&
            $proteinas == 0 &&
            $carboidratos == 0 &&
            $gorduras == 0 &&
            $ingredienteAntigo
        ) {

            $quantidadeAntiga = (float) (
                $ingredienteAntigo['quantidade'] ?? 0
            );


            /*
             * Se a quantidade antiga existir,
             * recalcula proporcionalmente.
             */
            if ($quantidadeAntiga > 0) {

                $fator = $quantidadeNova / $quantidadeAntiga;

                $calorias = round(
                    (float) ($ingredienteAntigo['calorias'] ?? 0) * $fator,
                    2
                );

                $proteinas = round(
                    (float) ($ingredienteAntigo['proteinas'] ?? 0) * $fator,
                    2
                );

                $carboidratos = round(
                    (float) ($ingredienteAntigo['carboidratos'] ?? 0) * $fator,
                    2
                );

                $gorduras = round(
                    (float) ($ingredienteAntigo['gorduras'] ?? 0) * $fator,
                    2
                );

            } else {

                /*
                 * Caso não exista quantidade antiga,
                 * mantém os valores anteriores.
                 */
                $calorias = (float) (
                    $ingredienteAntigo['calorias'] ?? 0
                );

                $proteinas = (float) (
                    $ingredienteAntigo['proteinas'] ?? 0
                );

                $carboidratos = (float) (
                    $ingredienteAntigo['carboidratos'] ?? 0
                );

                $gorduras = (float) (
                    $ingredienteAntigo['gorduras'] ?? 0
                );
            }


            $idApi = $ingredienteAntigo['idApi'] ?? $idApi;
        }


        /*
         * Salva as informações do ingrediente.
         */
        $infosNutricionais[] = [
            'idApi' => $idApi,
            'nome' => $nome,
            'quantidade' => $quantidadeNova,
            'calorias' => $calorias,
            'proteinas' => $proteinas,
            'carboidratos' => $carboidratos,
            'gorduras' => $gorduras
        ];
    }


    /*
     * ==========================================================
     * CALCULA OS TOTAIS
     * ==========================================================
     */

    $totalCalorias = 0;
    $totalProteinas = 0;
    $totalCarboidratos = 0;
    $totalGorduras = 0;


    foreach ($infosNutricionais as $info) {

        $totalCalorias += (float) ($info['calorias'] ?? 0);
        $totalProteinas += (float) ($info['proteinas'] ?? 0);
        $totalCarboidratos += (float) ($info['carboidratos'] ?? 0);
        $totalGorduras += (float) ($info['gorduras'] ?? 0);
    }


    /*
     * Mantém o mesmo formato utilizado no cadastro.
     */
    $infosParaSalvar = [
        'total' => [
            'calorias' => round($totalCalorias, 2),
            'proteinas' => round($totalProteinas, 2),
            'carboidratos' => round($totalCarboidratos, 2),
            'gorduras' => round($totalGorduras, 2)
        ],

        'ingredientes' => $infosNutricionais
    ];


    /*
     * ==========================================================
     * IMAGEM
     * ==========================================================
     */

    $nomeImagem = $receita['imagem'];

    $imagem = $this->request->getFile('imagem');

    if ($imagem && $imagem->isValid() && !$imagem->hasMoved()) {

        $nomeImagem = $imagem->getRandomName();

        $imagem->move(
            ROOTPATH . 'public/uploads',
            $nomeImagem
        );


        // Remove a imagem antiga
        if (!empty($receita['imagem'])) {

            $imagemAntiga = ROOTPATH .
                'public/uploads/' .
                $receita['imagem'];

            if (file_exists($imagemAntiga)) {
                unlink($imagemAntiga);
            }
        }
    }


    /*
     * ==========================================================
     * DADOS PARA ATUALIZAÇÃO
     * ==========================================================
     */

    $dados = [
        'titulo' => $titulo,
        'legenda' => $legenda,
        'imagem' => $nomeImagem,
        'tags' => json_encode($tags),
        'ingredientes' => json_encode($nomesIngredientes),
        'quantidadeIngredientes' => json_encode($quantidades),
        'infosNutricionais' => json_encode($infosParaSalvar)
    ];


    /*
     * ==========================================================
     * ATUALIZA NO BANCO
     * ==========================================================
     */

    if ($receitaModel->update($idReceita, $dados)) {

        return redirect()
            ->to(base_url('receita/visualizar/' . $idReceita))
            ->with(
                'sucesso',
                'Receita atualizada com sucesso!'
            );
    }


    return redirect()
        ->back()
        ->withInput()
        ->with(
            'erro',
            'Erro ao atualizar a receita.'
        );
}

}