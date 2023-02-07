<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'bots';
    protected $table            = 'tasks';
    protected $primaryKey       = 'id_task';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_task','task_id','task_status', 'task_propriry','task_offset','updated_at'
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

    function index($d1='',$d2='',$d3='')
        {

        }

    function task_remove($task)
        {
            $this->where('task_id',$task)->delete();
            return true;
        }

    function task($task)
        {
            $task = mb_strtoupper($task);
            $dt = $this
                ->where('task_id',$task)
                ->First();
            if ($dt == '')
                {
                    $dt['task_id'] = $task;
                    $dt['task_status'] = 1;
                    $dt['task_propriry'] = 1;
                    $dt['task_offset'] = 0;
                    $this->set($dt)->insert();
                }
            return $dt;
        }
}
