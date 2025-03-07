<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class EventsScheduleBlockParticipante extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_schedule_bloco_participante';
    protected $primaryKey       = 'id_bp';
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
        $EventsScheduleBlockTipo = new \App\Models\Gev3nt\EventsScheduleBlockTipo();
        $RSP = $EventsScheduleBlockTipo->orderby('tp_ordem')->findAll();

        $cp = 'n_nome, id_n, n_email, cb_nome, cb_sigla, bp_ordem, n_biografia';

        foreach($RSP as $id=>$tipo)
            {
                $dt = $this
                ->select($cp)
                ->join('events_names', 'bp_pessoa = id_n')
                ->join('corporateBody', 'n_afiliacao = id_cb', 'LEFT')
                ->where('bp_block', $ev)
                ->where('bp_funcao', $tipo['id_tp'])
                ->orderby('bp_ordem')
                ->findAll();
                $RSP[$id]['person'] = $dt;
            }
        $DT = $RSP;

        $RSP = [];
        foreach($DT as $id=>$line)
            {
                if ($line['person'] != [])
                    {
                        array_push($RSP,$line);
                    }
            }
        return $RSP;
    }
}
