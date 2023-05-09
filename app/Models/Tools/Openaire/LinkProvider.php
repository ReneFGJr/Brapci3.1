<?php

namespace App\Models\Tools\Openaire;

use CodeIgniter\Model;

class LinkProvider extends Model
{
    protected $DBGroup          = 'openaire';
    protected $table            = 'openaire_linkproviders';
    protected $primaryKey       = 'id_lk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lk', 'lk_doi', 'lk_status', 'lk_method', 'lk_result'
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

    function register($doi)
        {
            $sx = '';
            $dt = $this->where('lk_doi',$doi)->first();
            if ($dt=='')
                {
                    $dt['lk_doi'] = $doi;
                    $this->set($dt)->insert();
                    $sx .= lang('brapci.insered');
                } else {
                    $sx .= lang('brapci.already_registered');
                }
            return $sx;

        }
}
