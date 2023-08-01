<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
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

    function index($d1='',$d2='',$d3='',$d4='',$d5='')
        {
            $sx = '';
            return $sx;
        }

function resume($id)
    {
        $dt = $this->getId($id);

    }
        function getId($id=0)
        {
                $dt = $this
                    ->where('id_e',$id)
                    ->first();

                $cp = 'es_event';
                $dt['sections'] = $this
                    ->select($cp.', count(*) as total')
                    ->join('event_sections','es_event = id_e')
                    ->join('event_inscritos','ei_sub_event=id_es','left')
                    ->where('es_event',$id)
                    ->orderBy('es_data, es_hora_ini')
                    ->findAll();
                    echo $this->getlastquery();
                return $dt;
        }
        function subevents($ev=0,$cpf='')
        {
                $cp = '*';
                $dt = $this
                    ->select($cp)
                    ->join('event_sections','es_event = id_e')
                    ->join('event_inscritos','ei_cpf='.$cpf.' AND ei_sub_event=id_es','left')
                    ->where('es_event',$ev)
                    ->orderBy('es_data, es_hora_ini')
                    ->findAll();
                return $dt;
        }

    function events($type=0)
        {
                $cp = 'id_e,e_name,e_sigla,e_data_i,e_data_f,e_img';
                $dt = $this
                    ->select($cp)
                    ->join('event_sections','es_event = id_e')
                    ->where('es_active',$type)
                    ->groupBy($cp)
                    ->orderBy('e_data_i')
                    ->findAll();
                return $dt;
        }
}
