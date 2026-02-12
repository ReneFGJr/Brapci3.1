<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class ResearchProjectsCollaboratorsModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'research_projects_collaborators';
    protected $primaryKey       = 'id_rpc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'rpc_project',
        'rpc_user',
        'rpc_role'
    ];

    // Timestamps
    protected $useTimestamps = false; // created_at já é automático no banco
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validações
    protected $validationRules = [
        'rpc_project' => 'required|integer',
        'rpc_user'    => 'required|integer',
        'rpc_role'    => 'required|integer'
    ];

    protected $validationMessages = [
        'rpc_project' => [
            'required' => 'O projeto é obrigatório.',
            'integer'  => 'Projeto inválido.'
        ],
        'rpc_user' => [
            'required' => 'O usuário é obrigatório.',
            'integer'  => 'Usuário inválido.'
        ],
        'rpc_role' => [
            'required' => 'O papel do colaborador é obrigatório.',
            'integer'  => 'Papel inválido.'
        ]
    ];

    protected $skipValidation = false;

    /*
     |--------------------------------------------------------------------------
     | MÉTODOS AUXILIARES
     |--------------------------------------------------------------------------
     */

    /**
     * Retorna colaboradores de um projeto
     */
    public function roles()
        {
            return [
                1 => 'Administrador',
                2 => 'Colaborador',
                3 => 'Visualizador'
            ];
        }

    public function countByProject($projectId)
    {
        return $this->where('rpc_project', $projectId)->countAllResults();
    }

    public function getProjects($userId)
    {
        $cp = 'id_rpc, research_projects.id as id, project_name, description, status';
        return $this
                    ->select($cp)
                    ->join('research_projects', 'research_projects.id = rpc_project', 'left')
                    ->where('rpc_user', $userId)
                    ->orderBy('id_rpc', 'ASC')
                    ->findAll();
    }
    public function getByProject($projectId)
    {
        $cp = 'id_rpc, us_nome, us_institution, us_lastaccess, rpc_role';
        $dt = $this
                    ->select($cp)
                    ->join('brapci.users','id_us = rpc_user','left')
                    ->where('rpc_project', $projectId)
                    ->orderBy('id_rpc', 'ASC')
                    ->findAll();
        foreach ($dt as &$item) {
            $item['role_name'] = $this->roles()[$item['rpc_role']] ?? 'Desconecido';
        }
        return $dt;
    }

    /**
     * Retorna projetos de um usuário
     */
    public function getByUser($userId)
    {
        return $this->where('rpc_user', $userId)
                    ->orderBy('id_rpc', 'ASC')
                    ->findAll();
    }

    /**
     * Verifica se o usuário já está vinculado ao projeto
     */
    public function exists($projectId, $userId)
    {
        return $this->where([
                        'rpc_project' => $projectId,
                        'rpc_user'    => $userId
                    ])->first();
    }

    /**
     * Remove colaborador de um projeto
     */
    public function removeCollaborator($projectId, $userId)
    {
        return $this->where([
                    'rpc_project' => $projectId,
                    'rpc_user'    => $userId
                ])->delete();
    }
}