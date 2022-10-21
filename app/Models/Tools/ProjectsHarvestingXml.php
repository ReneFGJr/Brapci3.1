<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class ProjectsHarvestingXml extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_tools.projects_harvesting_xml';
    protected $primaryKey       = 'id_hx';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'hx_project', 'hx_id_lattes', 'hx_status', 'hx_updated', 'created_at'
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

    function register($idp, $lattes)
    {
        $sx = '';
        $dt = $this->where('hx_id_lattes', $lattes)->where('hx_project', $idp)->findAll();
        if (count($dt) == 0) {
            $dt['hx_project'] = $idp;
            $dt['hx_id_lattes'] = $lattes;
            $dt['hx_status'] = 0;
            $dt['hx_updated'] = '1900-01-01 00:00:00';
            $this->set($dt)->insert();
            $sx .= $lattes . ' inserted';
        } else {
            $sx .= $lattes . ' skipped';
        }
    }
}
