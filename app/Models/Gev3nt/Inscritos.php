<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Inscritos extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event_inscritos';
    protected $primaryKey       = 'id_ei';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ei','ei_cpf','ei_sub_event',
        'ei_situacao'
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

    function register($cpf,$id,$sta)
        {
            $dt = $this
            ->where('ei_cpf',$cpf)
            ->where('ei_sub_event',$id)
            ->first();

            if ($dt=='')
                {
                    $dt['ei_cpf'] = $cpf;
                    $dt['ei_sub_event'] = $id;
                    $dt['ei_situacao'] = 1;
                    $this->set($dt)->insert($dt);
                } else {
                    $dt['ei_situacao'] = $sta;
                    $this->set($dt)->where('id_ei',$dt['id_ei']);
                }
        }

    
}
