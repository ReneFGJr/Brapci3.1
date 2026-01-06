<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class ResearchProjectModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'research_projects';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'project_name',
        'description',
        'owner_id',
        'status',
        'team'
    ];

    protected $validationRules = [
        'project_name' => 'required|min_length[3]|max_length[255]',
        'owner_id'     => 'required|integer',
    ];

    protected $validationMessages = [
        'project_name' => [
            'required' => 'O nome do projeto é obrigatório.'
        ],
        'owner_id' => [
            'required' => 'O dono do projeto é obrigatório.'
        ]
    ];

    /**
     * Retorna projetos por dono
     */
    public function getByOwner(int $ownerId)
    {
        return $this->findAll();
        //return $this->where('owner_id', $ownerId)->findAll();
    }

    /**
     * Retorna projetos por situação
     */
    public function getByStatus(string $status)
    {
        return $this->where('status', $status)->findAll();
    }

    /**
     * Insere ou atualiza equipe (array → JSON)
     */
    public function updateTeam(int $projectId, array $team)
    {
        return $this->update($projectId, [
            'team' => json_encode($team, JSON_UNESCAPED_UNICODE)
        ]);
    }
}
