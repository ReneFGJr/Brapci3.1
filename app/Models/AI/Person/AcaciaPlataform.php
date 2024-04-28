<?php

namespace App\Models\AI\Person;

use CodeIgniter\Model;

class AcaciaPlataform extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'acaciaplataforms';
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

    public $url = 'https://plataforma-acacia.org/profile/';

    function standard($name='')
        {
            $url = 'https://plataforma-acacia.org/profile/';

            $name = ascii($name);
            $name = mb_strtolower($name);
            $name = troca($name,' ','-');

            $url = $url.$name;
            return $url;

        }
}
