<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class Simori extends Model
{
    protected $DBGroup    = 'simori';
    protected $table      = 'repository';
    protected $primaryKey = 'id';

    protected $allowedFields = [

    ];

    protected $useTimestamps = false;

    function getProjectsID()
    {
        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }

    function contarRepositorios()
    {
        // Conta o número total de repositórios na tabela
        return $this->countAll();
    }
}
