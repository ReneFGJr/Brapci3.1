<?php

namespace App\Models\Cron;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'bots';
    protected $table            = 'cron';
    protected $primaryKey       = 'id_cron';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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
    public $agent = 0;

    function index($d1,$d2)
        {
            $web = agent();
            if ($web == '') { $web = 0; }
            $this->agent = $web;

            $sep = cr();
            $type = '';
            if ($web) { $sep = '<br>'; $type = 'Web version'; }
            echo "CRON ".VERSION_BOT.$sep;
            echo "================== $type".$sep;

            $d1 .= get("d1");

            switch($d1)
                {
                    case('list'):
                        $this->list();
                        break;
                    case('next'):
                        $this->next();
                        break;
                }
            exit;
        }



    function list()
        {
            $web = $this->agent;
            $dt = $this->findAll();

        }
}
