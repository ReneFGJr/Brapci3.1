<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class About extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cms';
    protected $primaryKey       = 'id_cms';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cms', 'cms_ref', 'cms_pos',
        'cms_text', 'cms_lang'
    ];
    protected $typeFields    = [
        'hidden', 'string', '[1-99]',
        'text', 'op:pt_BR:pt_BR'
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

    function about()
        {
            $Indicators = new \App\Models\Base\AboutIndicadores();
            $Indicators->makeIndicators();
            $di = $Indicators->indicators();
            $sx = '';
            $dt = $this
            ->where('cms_ref','ABOUT')
            ->orderBy('cms_pos')
            ->findAll();

            foreach($dt as $id=>$line)
                {
                    $sx .= bsc($line['cms_text'],12,'mt-2');
                }

            foreach($di as $var=>$vlr)
                {
                    $sx = troca($sx,'{'.$var.'}',$vlr);
                }
            $sx = bs($sx);
            return $sx;
        }
}
