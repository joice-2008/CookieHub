<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    private $apiKey = 'e853d84903274ef19d19aa48b6bc1fcd';

    public function ingrediente($nome)
    {
        // 1. Buscar ID do ingrediente
        $urlSearch = "https://api.spoonacular.com/food/ingredients/search?query=" 
                    . urlencode($nome) 
                    . "&apiKey=" . $this->apiKey;

        $response = file_get_contents($urlSearch);
        $data = json_decode($response, true);

        if (empty($data['results'])) {
            return $this->respond([
                'erro' => 'Ingrediente não encontrado'
            ]);
        }

        $id = $data['results'][0]['id'];

        // 2. Buscar nutrientes
        $urlInfo = "https://api.spoonacular.com/food/ingredients/" 
                 . $id 
                 . "/information?amount=100&unit=grams&apiKey=" 
                 . $this->apiKey;

        $responseInfo = file_get_contents($urlInfo);
        $info = json_decode($responseInfo, true);

        // 3. Extrair calorias
        $calorias = 0;

        foreach ($info['nutrition']['nutrients'] as $nutriente) {
            if ($nutriente['name'] == 'Calories') {
                $calorias = $nutriente['amount'];
            }
        }

        // 4. Retorno final
        return $this->respond([
            'ingrediente' => $nome,
            'calorias_100g' => $calorias,
            'proteina' => $this->getNutriente($info, 'Protein'),
            'carboidratos' => $this->getNutriente($info, 'Carbohydrates'),
            'gordura' => $this->getNutriente($info, 'Fat')
        ]);
    }

    private function getNutriente($info, $nome)
    {
        foreach ($info['nutrition']['nutrients'] as $nutriente) {
            if ($nutriente['name'] == $nome) {
                return $nutriente['amount'];
            }
        }
        return 0;
    }

    public function testeTraducao()
{
    helper('translate');

    $teste = traduzirLibre("batata doce", "pt", "en");

    return $this->respond([
        'resultado' => $teste
    ]);
}
}