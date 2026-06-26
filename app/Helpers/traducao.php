<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    public function ingrediente($nome)
    {
        helper('translate');

        // 1. recebe em português
        $ingrediente_pt = $nome;

        // 2. traduz para inglês
        $ingrediente_en = traduzirLibre($ingrediente_pt, "pt", "en");

        // 3. chama API de nutrientes (ex: Spoonacular ou outra)
        $url = "https://api.exemplo.com/search?query="
             . urlencode($ingrediente_en);

        $json = file_get_contents($url);
        $data = json_decode($json, true);

        // 4. traduz resultado de volta (opcional)
        $nome_pt = traduzirLibre($data['name'] ?? '', "en", "pt");

        return $this->response->setJSON([
            "ingrediente_original" => $ingrediente_pt,
            "ingrediente_en" => $ingrediente_en,
            "nome_traduzido" => $nome_pt,
            "calorias" => $data['calories'] ?? null
        ]);
    }
}