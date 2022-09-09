<?php

namespace App\Models\Crawler\Webcrawler;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.webcrawler';
    protected $primaryKey       = 'id_wc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cron', 'cron_acron', 'cron_name',
        'cron_cmd', 'cron_day', 'cron_exec',
        'cron_timeout', 'cron_prior'
    ];
    protected $typeFields    = [
        'hidden', 'string*', 'string*',
        'text', 'op:1&every_day:2&last_day_month:15&every_hour', 'op:webcrawler&webcrawler:php&php:pythob&python*',
        'op:30&30s:60&60s:90&90s:120&120s*', '[1-99]*',
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

    function index($act, $id)
    {
        $sx = h('Webcrawler',3);

        return $sx;
    }



}
