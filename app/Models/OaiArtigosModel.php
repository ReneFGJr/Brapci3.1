<?php

namespace App\Models;

use CodeIgniter\Model;

class OaiArtigosModel extends Model
{
    protected $DBGroup = 'oai_server';
    protected $table = 'oai_artigos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'repository',
        'section',
        'title',
        'authors',
        'abstract',
        'keywords'
    ];

    protected $useTimestamps = false;

    /**
     * Obter artigos de um repositório
     */
    public function getByRepository($repositoryId)
    {
        return $this->where('repository', $repositoryId)->findAll();
    }

    /**
     * Obter artigos de um set específico
     */
    public function getBySet($setId, $repositoryId = null)
    {
        $builder = $this->where('section', $setId);

        if ($repositoryId) {
            $builder->where('repository', $repositoryId);
        }

        return $builder->findAll();
    }

    /**
     * Obter artigos de um repositório e set
     */
    public function getByRepositoryAndSet($repositoryId, $setId)
    {
        return $this->where('repository', $repositoryId)
                    ->where('section', $setId)
                    ->findAll();
    }

    /**
     * Contar artigos por set em um repositório
     */
    public function countBySet($repositoryId, $setId)
    {
        return $this->where('repository', $repositoryId)
                    ->where('section', $setId)
                    ->countAllResults();
    }

    /**
     * Obter artigo por ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Obter artigos com filtro por período (datestamp)
     */
    public function getByDateRange($repositoryId, $startDate = null, $endDate = null, $setId = null)
    {
        $builder = $this->where('repository', $repositoryId);

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        if ($setId) {
            $builder->where('section', $setId);
        }

        return $builder->findAll();
    }
}
