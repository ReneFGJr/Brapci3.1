<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class BrapciWorksModel extends Model
{
    protected $DBGroup    = 'brapci_cited';
    protected $table      = 'cited_works';
    protected $primaryKey = 'id';

    protected $allowedFields = [

    ];

    protected $useTimestamps = false;

    function show_cited_work($id)
    {
        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }
}
