<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class SourceType extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sourcetypes';
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

    function index_sourcers($source = array(), $id = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'class.nm';
        if (file_exists($file)) {
            $term = file_get_contents($file);
            if (isset($source[$term])) {
                $source[$term]++;
            } else {
                $source[$term] = 1;
            }
        }
        return $source;
    }
}
