<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class CitedArticleModel extends Model
{
    protected $DBGroup          = 'brapci_cited';
    protected $table            = 'cited_article';
    protected $primaryKey       = 'id_ca';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    /**
     * Campos permitidos para INSERT / UPDATE
     */
    protected $allowedFields = [
        'ca_id',
        'ca_rdf',
        'ca_doi',
        'AUTHORS',
        'ca_journal',
        'ca_journal_origem',
        'ca_year',
        'ca_year_origem',
        'ca_vol',
        'ca_nr',
        'ca_pag',
        'ca_tipo',
        'ca_text',
        'ca_status',
        'ca_ordem',
        'ca_ai',
        'ca_update_at'
    ];

    /**
     * Datas
     */
    protected $useTimestamps = false;
    protected $createdField  = 'ca_created_at';
    protected $updatedField  = 'ca_update_at';

    /**
     * Validações básicas
     */
    protected $validationRules = [
        'ca_id'    => 'permit_empty|max_length[20]',
        'ca_doi'   => 'permit_empty|max_length[120]',
        'ca_year'  => 'permit_empty|integer',
        'ca_status'=> 'permit_empty|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /* =====================================================
     * Métodos auxiliares
     * ===================================================== */

    /**
     * Retorna artigos citados por DOI
     */
    public function getByDoi(string $doi)
    {
        return $this->where('ca_doi', $doi)->findAll();
    }

    /**
     * Retorna artigos por ano
     */
    public function getByYear(int $year)
    {
        return $this->where('ca_year', $year)->findAll();
    }

    /**
     * Retorna artigos ativos
     */
    public function getAtivos()
    {
        return $this->where('ca_status', 1)
                    ->orderBy('ca_year', 'DESC')
                    ->findAll();
    }

    /**
     * Busca textual simples no campo de referência
     */
    public function searchText(string $term)
    {
        return $this->like('ca_text', $term)
                    ->orderBy('ca_year', 'DESC')
                    ->findAll();
    }

    /**
     * Atualiza status do artigo citado
     */
    public function setStatus(int $id, int $status): bool
    {
        return $this->update($id, [
            'ca_status'    => $status,
            'ca_update_at' => date('Y-m-d')
        ]);
    }
}
