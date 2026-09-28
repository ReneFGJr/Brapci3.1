<?php

namespace App\Models\Pensador;

use CodeIgniter\Model;

class Pensador extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'pensadores';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $useTimestamps = false;

    protected $allowedFields = [
        'nome',
        'nome_citacao',
        'link_wikipedia',
        'link_wikidata',
    ];

    protected $validationRules = [
        'nome' => 'required|max_length[255]',
        'nome_citacao' => 'required|max_length[255]',
        'link_wikipedia' => 'permit_empty|valid_url_strict[http,https]|max_length[2048]',
        'link_wikidata' => 'permit_empty|valid_url_strict[http,https]|max_length[2048]',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
