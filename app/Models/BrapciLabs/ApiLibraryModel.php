<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class ApiLibraryModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'api_library';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nome',
        'endpoint',
        'metodo',
        'parametros',
        'headers',
        'exemplo_request',
        'exemplo_response',
        'descricao',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
