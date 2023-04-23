<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class CronLogs extends Model
{
    protected $DBGroup          = 'bots';
    protected $table            = 'cron_logs';
    protected $primaryKey       = 'id_log';
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

    function show($d1,$offset)
        {
            $dt = $this
                ->where('log_type',$d1)
                ->orderBy('id_log desc')
                ->findAll($offset,0);
            $sx = h(lang('brapci.bots_lastlogs'),6);
            $sx .= '<span class="small"><tt>';
            foreach($dt as $id=>$line)
            {
            $sx .= stodbr($line['log_data']);
            $sx .= ' ';
            $sx .= substr($line['log_data'],11,5);
            $sx .= '<br>';
            }
            $sx .= '</tt></span>';
            return $sx;
        }
}
