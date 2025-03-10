<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Harvesting extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'harvestings';
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

    function painel($dt)
        {
            $ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();

            $sx = h('brapci.Painel',4);

            $sx .= $ListIdentifiers->resume($dt['id_jnl']);

            return $sx;
        }
}
