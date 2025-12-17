<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class CodebookModel extends Model
{
    protected $DBGroup    = 'brapci_labs';
    protected $table      = 'project_codebook';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'project_id',
        'title',
        'content',
        'tags',
        'created_by'
    ];

    protected $validationRules = [
        'project_id' => 'required|integer',
        'title'      => 'required|min_length[3]|max_length[255]',
        'content'    => 'required|min_length[3]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'O título da anotação é obrigatório.'
        ],
        'content' => [
            'required' => 'O conteúdo da anotação é obrigatório.'
        ]
    ];

    /**
     * Retorna todas as anotações de um projeto
     */
    public function getByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Retorna uma anotação específica do projeto
     */
    public function getOne(int $id, int $projectId): ?array
    {
        return $this->where([
            'id' => $id,
            'project_id' => $projectId
        ])->first();
    }

    /**
     * Insere anotação já vinculada ao projeto ativo
     */
    public function createForProject(
        int $projectId,
        string $title,
        string $content,
        ?array $tags = null,
        ?int $userId = null
    ): int|false {

        return $this->insert([
            'project_id' => $projectId,
            'title'      => $title,
            'content'    => $content,
            'tags'       => $tags ? json_encode($tags, JSON_UNESCAPED_UNICODE) : null,
            'created_by' => $userId
        ]);
    }

    function countByProject(int $projectId): int
    {
        return $this->where('project_id', $projectId)->countAllResults();
    }


}
