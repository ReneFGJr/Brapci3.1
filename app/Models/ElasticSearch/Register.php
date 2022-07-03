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
            $dt = $RDF->le($id);
            $sx = $this->register_article($dt);
        }

    function register_article($dt)
        {
            $API = new \App\Models\ElasticSearch\API();
            echo "Article";
            $type = 'work';
            $id = 1;
            $dt = array();
            $dt['id'] = 1;
            $dt = json_encode($dt);
            $rst = $API -> call($type . '/' . $id, 'PUT', $dt);
            pre($rst);
        }
}
