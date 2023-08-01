<?php

namespace App\Models\Authority\API;

use CodeIgniter\Model;

class AuthConcept extends Model
{
    protected $DBGroup          = 'authority';
    protected $table            = 'auth_concept';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_c', 'c_class', 'c_prefName',
        'c_cpf','c_lattes','c_cpf','c_email',
        'c_email_alt'
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

    function register($class,$prefLabel,$cpf='',$lattes='')
        {
            $dt = $this
                ->where('c_class',$class)
                ->where('c_prefName', $prefLabel)
                ->first();
            if ($dt == '')
                {
                    $dt['c_class'] = $class;
                    $dt['c_prefName'] = $prefLabel;
                    $dt['c_cpf'] = $cpf;
                    $dt['c_lattes'] = $lattes;
                    $idc = $this->set($dt)->insert();
                } else {
                    $idc = $dt['id_c'];
                }
            return $idc;
        }
    function remissive($id1,$id2)
        {
            $dt1 = $this->find($id2);
            if ($dt1 != '')
                {
                    $dt = $this->find($id1);
                    $dt['c_use'] = $id2;
                    $this->set($dt)->where('id_c',$id1)->update();
                }
            return true;
        }
}
