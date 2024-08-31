<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class EventsSchedule extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_schedule';
    protected $primaryKey       = 'id_sch';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sch',
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

    function agenda($ev)
        {
            $EventsScheduleBlock = new \App\Models\Gev3nt\EventsScheduleBlock();
            $dt = $this
                ->select('sch_day, id_sch')
                ->where('sch_event',$ev)
                ->findAll();
            $RSP = $dt;

            $RSP = [];

            foreach($dt as $id=>$line)
                {
                    $line['bloco'] = $EventsScheduleBlock->le($line['id_sch']);

                    array_push($RSP,$line);
                }
            return $RSP;

        }
}
