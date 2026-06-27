<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Stichoza\GoogleTranslate\GoogleTranslate;

class Api extends ResourceController
{
    private $chaveApi = 'e853d84903274ef19d19aa48b6bc1fcd';

    public function ingrediente($nomeIngrediente)
    {
        $tradutor = new GoogleTranslate();
        $tradutor->setSource('pt');
        $tradutor->setTarget('en');

        $ingredienteEmIngles = $tradutor->translate($nomeIngrediente);

        $urlBusca = "https://api.spoonacular.com/food/ingredients/search?query="
            . urlencode($ingredienteEmIngles)
            . "&apiKey=" . $this->chaveApi;

        $respostaBusca = @file_get_contents($urlBusca);
        $dadosBusca = json_decode($respostaBusca, true);

        if (empty($dadosBusca['results'])) {
            return $this->respond([
                'erro' => 'Ingrediente não encontrado'
            ]);
        }

        $idIngrediente = $dadosBusca['results'][0]['id'];

        $urlInformacoes = "https://api.spoonacular.com/food/ingredients/"
            . $idIngrediente
            . "/information?amount=100&unit=grams&apiKey="
            . $this->chaveApi;

        $respostaInformacoes = @file_get_contents($urlInformacoes);
        $informacoes = json_decode($respostaInformacoes, true);

        if (!$informacoes || !isset($informacoes['nutrition']['nutrients'])) {
            return $this->respond([
                'erro' => 'Erro ao obter informações nutricionais'
            ]);
        }

        $calorias = $this->getNutriente($informacoes, 'Calories');

        $tradutor->setSource('en');
        $tradutor->setTarget('pt');

        $ingredienteEmPortugues = $tradutor->translate($informacoes['name']);

        return $this->respond([
            'ingrediente' => $nomeIngrediente,
            'calorias_100g' => $calorias,
            'proteina' => $this->getNutriente($informacoes, 'Protein'),
            'carboidratos' => $this->getNutriente($informacoes, 'Carbohydrates'),
            'gordura' => $this->getNutriente($informacoes, 'Fat')
        ]);
    }

    private function getNutriente($informacoes, $nomeNutriente)
    {
        foreach ($informacoes['nutrition']['nutrients'] as $nutriente) {
            if ($nutriente['name'] == $nomeNutriente) {
                return $nutriente['amount'];
            }
        }
        return 0;
    }
}