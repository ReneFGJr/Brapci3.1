<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class IssuesWorks extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_issue_work';
    protected $primaryKey       = 'id_siw';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siw','siw_journal','siw_issue',
        'siw_section','siw_work_rdf','update_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function saving($dt)
        {
            $dt = $this->where('siw_work_rdf',$dd['idw'])->findAll();
            if (count($dt) == 0)
                {
                    $this->set($dt)->insert();
                }
        }

    function check($dt)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $idr = $dt['is_source_issue'];

            $dt = $RDF->le_data($idr);
            $dt = $dt['data'];
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];                    
                    $class = trim($line['c_class']);
                    if ($class == 'hasIssueOf')
                        {           
                            $dd['siw_work_rdf'] = $line['d_r2'];
                            $dd['siw_journal'] = $dt['is_source'];
                            $dd['siw_journal_rdf'] = $dt['is_source_rdf'];
                            $dd['siw_section'] = 0;
                            $dd['siw_issue'] = $idr;
                            $this->saving($dd);
                        }
                }
        }
}
