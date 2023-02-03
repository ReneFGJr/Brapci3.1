<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Authors extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'authors';
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

    function index_auths($auth = array(), $id = '')
    {
        return "";
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'authors.json';
        if (file_exists($file)) {
            $dt = file_get_contents($file);
            $dt = json_decode($dt);

            for ($r = 0; $r < count($dt); $r++) {
                $t = (array)$dt[$r];
                if (strlen($t['name']) > 0) {
                    $term = trim($t['name']);
                    $id = $t['id'];
                    $term .= ';' . $id;
                    if (isset($auth[$term])) {
                        $auth[$term]++;
                    } else {
                        $auth[$term] = 1;
                    }
                }
            }
        }
        return $auth;
    }
}