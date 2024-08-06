<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Users extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'events_names';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_n','n_nome','b_cracha','n_email',
        'n_cpf','n_orcid'
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
            ->where('n_email',$cpf)
            ->first();

            if ($dt=='')
                {
                    $dt['n_nome'] = $cpf;
                    $dt['n_cracha'] = $id;
                    $dt['n_email'] = 1;
                    $dt['n_nome'] = 1;
                    $dt['n_nome'] = 1;
                    $dt['n_nome'] = 1;
                    $this->set($dt)->insert($dt);
                } else {
                    $dt['ei_situacao'] = $sta;
                    $this->set($dt)->where('id_ei',$dt['id_ei']);
                }
        }


}
