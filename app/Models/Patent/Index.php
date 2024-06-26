<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
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

    function cron()
        {
            echo "Cron - Patent BOT\n";
            $RPIIssue = new \App\Models\Patent\RPIIssue;
            $RPI_import = new \App\Models\Patent\RPIImport;
            $dt = $RPIIssue->orderby('rpi_status')->first();
            if ($dt != '')
                {
                    $d1 = $dt['rpi_nr'];
                    $RPI_import->proccess($d1);
                }

            //echo $RPIIssue->action($dt[0]);
            exit;
        }

    function index($act='',$d1='',$d2='',$d3='')
        {
            $sx = '';
            switch($act)
                {
                    case 'harvesting':
                        $RPI = new \App\Models\Patent\RPI;
                        $sx = $RPI->harvesting($d1);
                        break;
                    default:
                        $sx .= h($act);
                }
            return $sx;
        }
}
