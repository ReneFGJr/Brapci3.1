<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFprefix extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_prefix';
    protected $primaryKey       = 'id_prefix';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'prefix_ref', 'prefix_url', 'prefix_ativo',
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

    function register($prefix,$url,$active=1)
        {
            $dt = $this->getPrefix($prefix);
            if ($dt == null)
                {
                    $d = [];
                    $d['prefix_ref'] = $prefix;
                    $d['prefix_url'] = $url;
                    $d['prefix_ativo'] = $active;
                    $this->set($d)->insert();
                }
            return "";
        }
    function getPrefix($prefix)
        {
            return $this->where('prefix_ref', $prefix)->first();
        }
    function getPrefixID($prefix)
    {
        $dt = $this->getPrefix($prefix);
        if (isset($dt['id_prefix']))
            {
                return $dt['id_prefix'];
            } else {
                return 0;
            }
    }
}
