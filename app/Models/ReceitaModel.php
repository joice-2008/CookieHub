<?php
namespace App\Models;

use CodeIgniter\Model;

class ReceitaModel extends Model
{
    protected $table = 'receita';

    protected $primaryKey = 'idReceita';

    protected $allowedFields = [
        'titulo',
        'legenda',
        'imagem',
        'tags',
        'ingredientes',
        'quantidadeIngredientes',
        'infosNutricionais',
        'data',
        'idUsuario',
        'status'
    ];
}