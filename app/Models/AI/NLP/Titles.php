<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Titles extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'abstracts';
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


    function check_next()
    {
        $BUGS = new \App\Models\Functions\Bugs();
        $task = 'CHECK_TITLES';
        $limit = 2000;
        $BOTS = new \App\Models\Bots\Index();
        $dt = $BOTS->task($task);
        $BOTS->task_remove($task);
        return "FIM";
    }
}
