<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class Cron extends Model
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

    function index($d1,$d2,$d3)
        {
            $sx = '';
            $sx .= "[$d1,$d2,$d3]";

            switch($d1)
                {
                    case 'view':
                        $sx .= $this->viewid($d2,$d3);
                        break;
                    default:
                        $sx .= $this->show_task();
                }
            return $sx;
        }

    function viewid($d1,$d2)
        {
            $CronLogs = new \App\Models\Bots\CronLogs();
            $dt = $this->where('cron_acron',$d1)->first();
            $sx = $this->header($dt);
            $sa = $CronLogs->show($d1,20);
            $sx .= bsc('',9).bsc($sa,3,'text-end');
            $sx = bs($sx);
            return $sx;
        }

    function header($dt)
        {
            $sx = '';
            $sx .= bsc(h($dt['cron_name'],2),10);
            $sx .= bsc(h($dt['cron_acron'],2),2,'text-end');

            $sx .= bsc($dt['cron_exec'],2);
            $sx .= bsc($dt['cron_day'].' day',2);
            $sx .= bsc($dt['cron_timeout'].' timeout',2);
            $sx .= bsc($dt['cron_prior'].' priority',2);
            $sx .= bsc('',4);
            $sx = bs($sx);
            return $sx;
        }

    function show_task()
        {
            $dt = $this->findAll();
            $sx = '<ul>';
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'/admin/cron/view/'.$line['cron_acron'].'">';
                    $linka = '</a>';
                    $sx .= '<li>';
                    $sx .= $link.$line['cron_name'].$linka;
                    $sx .= '</li>';
                }
            $sx .= '</ul>';
            $sx = bs(bsc($sx,12));
            return $sx;
        }
}
