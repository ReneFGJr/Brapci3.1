<?php

namespace App\Models\AI\Geo\Institution;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    function code($name)
        {
            $name = trim(uppercaseSQL($name));
            $uf = array('IBICT'=>'rj',
            'UFMG'=>'mg',
            'UFF'=>'rj',
            'UFSC'=>'sc',
            'UNB'=> 'df',
            'UNICAMP'=>'sp',
            'UNIFESP'=>'sp',
            'UNIRIO'=>'rj',
            'UNISINOS'=>'rs',
            'UFPE'=>'pe',
            'UFBA'=>'ba',
            'UNESP'=>'sp',
            );

            if (isset($uf[$name]))
                {
                    return($uf[$name]);
                } else {
                    return('xx');
                }

        }
}
