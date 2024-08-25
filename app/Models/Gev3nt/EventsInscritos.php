<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class EventsInscritos extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_inscritos';
    protected $primaryKey       = 'id_ein';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ein',
        'ein_user',
        'ein_event',
        'ein_tipo',
        'ein_pago',
        'ein_pago_em',
        'ein_recibo'
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

    function register($ev, $tp, $user)
    {
        $Events = new \App\Models\Gev3nt\EventsInscricoesTipos();
        $dt = $Events
            ->where("ein_user", $user)
            ->where('ein_tipo', $tp)
            ->first();
        if ($dt == []) {
            $dd = [];
            $dt['ein_event'] = $ev;
            $dt['ein_tipo'] = $tp;
            $dt['ein_user'] = $user;
            $Events->set($dt)->insert();
        }
    }

    function Subscribe($id = 0, $user = 0)
    {
        $EventsInscricoesTipos = new \App\Models\Gev3nt\EventsInscricoesTipos();
        $event_type = get("event_type");
        if ($event_type != '')
            {
                $RSP = $this->register($id, $event_type, $user);
            }

        $RSP = $EventsInscricoesTipos->inscricoes_type($id, $user);
        return $RSP;
    }
}
