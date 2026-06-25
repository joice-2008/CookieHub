<?php

namespace App\Controllers;

class Api extends BaseController
{
    public function buscar($nome)
    {
        $nome = urlencode($nome);

        $url = "https://world.openfoodfacts.org/cgi/search.pl?search_terms={$nome}&search_simple=1&action=process&json=1";

        $json = file_get_contents($url);

        $dados = json_decode($json, true);

        return $this->response->setJSON($dados);
    }
}