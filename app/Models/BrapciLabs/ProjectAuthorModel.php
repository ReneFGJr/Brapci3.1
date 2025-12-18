<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class ProjectAuthorModel extends Model
{
    protected $DBGroup    = 'brapci_labs';
    protected $table      = 'project_authors';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    //protected $useTimestamps = true;
    //protected $createdField  = 'created_at';
    //protected $updatedField  = null;

    protected $allowedFields = [
        'nome',
        'lattes_id',
        'brapci_id',
        'project_id',
        'created_at'
    ];

    protected $validationRules = [];

    protected $validationMessages = [];

    /* =========================================
       MÉTODOS ÚTEIS
    ========================================= */

    public function countByProject(int $projectId): int
    {
        return $this->where('project_id', $projectId)
            ->countAllResults();
    }

    /**
     * Retorna autores vinculados a um projeto
     */
    public function getByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    /**
     * Retorna autor pelo BRAPCI ID
     */
    public function getByBrapciId(int $brapciId): ?array
    {
        return $this->where('brapci_id', $brapciId)->first();
    }

    /**
     * Verifica se autor já está vinculado ao projeto
     */
    public function existsInProject(string $nome, int $projectId): bool
    {
        $rsp = $this->where([
            'project_id' => $projectId,
            'nome'       => $nome
        ])->countAllResults() > 0;
        return $rsp;
    }
}
