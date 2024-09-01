<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Corporatename extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'corporateBody';
    protected $primaryKey       = 'id_cb';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cb',
        'cb_nome',
        'cb_sigla',
        'cb_pais'
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

    function le($id)
        {
            $dt = $this->where('id_cb',$id)->first();
            return $dt;
        }

    function searchName($name='')
        {
            $name = explode(' ',$name);
            $this->select('*');
            foreach($name as $id=>$n)
                {
                    $this->like('cb_nome',$n);
                }
            $this->orderby('cb_nome ');
            $dt = $this->findAll();
            pre($dt);
            return $dt;
        }
}
