<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Register extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'registers';
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

    function register($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . '/' . 'elastic.json';
        if (file_exists($file)) {
            $API = new \App\Models\ElasticSearch\API();
            $type = 'proceeding';
            $dt = file_get_contents($file);
            $dt = (array)json_decode($dt);
            $dt['id'] = $id;
            //$dt['_id'] = $id;
            $dt['id_jnl'] = 75;
            $rst = $API->call($type . '/' . $id, 'PUT', $dt);
            jslog("Elastic Export: ".$id);
            echo h($type . '/' . $id);
            pre($dt);
            exit;
        }
    }
}
