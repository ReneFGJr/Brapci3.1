<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\IdentifyModel;

class OaiListSetsModel extends Model
{
    protected $DBGroup = 'oai_server';
    protected $table = 'oai_listsets';
    protected $primaryKey = 'id_s';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        's_cod',
        's_description'
    ];

    protected $useTimestamps = false;

    /**
     * Obter todos os sets com contagem de artigos
     */
    public function getAllSets($repositoryId = null)
    {
        $query = 'SELECT ls.id_s, ls.s_cod, ls.s_description, COUNT(oa.id) as count_artigos
                  FROM oai_listsets ls
                  LEFT JOIN oai_artigos oa ON ls.id_s = oa.section';

        if ($repositoryId) {
            $query .= ' WHERE oa.repository = ' . (int)$repositoryId;
        }

        $query .= ' GROUP BY ls.id_s, ls.s_cod, ls.s_description';

        $result = $this->db->query($query);
        return $result->getResultArray();
    }

    public function getSetsByRepository($repositoryPath)
    {
        // Primeiro, obter o ID do repositório pelo path
        $identifyModel = new IdentifyModel();
        $repository = $identifyModel->getByPath($repositoryPath);

        if (!$repository) {
            return [];
        }

        return $this->getAllSets($repository['id']);
    }

    /**
     * Obter um set específico com seus artigos
     */
    public function getSetWithArticles($setId, $repositoryId = null)
    {
        $builder = $this->db->table('oai_listsets ls');
        $builder->select('ls.*, oa.id as artigo_id, oa.title, oa.section');
        $builder->join('oai_artigos oa', 'ls.id_s = oa.section', 'left');
        $builder->where('ls.id_s', $setId);

        if ($repositoryId) {
            $builder->where('oa.repository', $repositoryId);
        }

        return $builder->get()->getResultArray();
    }
}
