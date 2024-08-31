<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class EventsScheduleBlock extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_schedule_bloco';
    protected $primaryKey       = 'id_esb';
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

    function le($ev)
    {
        $EventsScheduleBlockParticipante = new \App\Models\Gev3nt\EventsScheduleBlockParticipante();
        $dt = $this
            ->join('event_local', 'esb_local = id_lc')
            ->where('esb_day', $ev)
            ->findAll();

        foreach($dt as $id=>$line)
            {
                $dt[$id]['participantes'] = $EventsScheduleBlockParticipante->le($line['id_esb']);
            }
        return $dt;
    }
}
