<?php

namespace App\Models\Crawler\Webcrawler;

use CodeIgniter\Model;

class CrawlerTaskUrl extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.crawler_task_url';
    protected $primaryKey       = 'id_tsk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tsk', 'tsk_task', 'tsk_propriety',
        'tsk_value', 'tsk_status', 'tsk_father'
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

    function register($dt)
        {
            $da = $this->where('tsk_value', $dt['tsk_value'])
                ->where('tsk_task',$dt['tsk_task'])
                ->First();
            if ($da == '')
                {
                    $this->insert($dt);
                }
        }
}
