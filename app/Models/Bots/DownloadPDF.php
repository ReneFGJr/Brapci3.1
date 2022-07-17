<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class DownloadPDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.article_to_download';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_d', 'd_article', 'd_method',
        'd_lasupdate'
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

    function toHarvesting($id)
    {
        $dt = $this->where('d_article', $id)->findAll();
        if (count($dt) == 0) {
            $dt['d_article'] = $id;
            $dt['d_method'] = 0;
            $dt['d_lasupdate'] = date("Y-m-d");
            $this->insert($dt);
        }
    }
}