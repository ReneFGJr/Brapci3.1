<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class EventsInscricoesTipos extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_inscricoes';
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

    function inscricoes_type($id,$user=0)
        {
            $dt = $this
                ->join('event_inscritos', '(id_ei = ein_tipo) and (ein_user = '.$user.')','LEFT')
                ->where('ei_event',$id)
                ->findAll();
            return $dt;
        }
}
