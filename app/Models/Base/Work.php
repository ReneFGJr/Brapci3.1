<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Work extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work';
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

    function showHTML($dt)
    {
        $sx = view('RDF/work', $dt);
        return $sx;
    }

    function show($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $dd = $dt['data'];
        for ($r = 0; $r < count($dd); $r++) {
            $line = $dd[$r];
            $class = $line['c_class'];
            //echo '==>'.$class;
        }
    }

    function show_reference($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $sx = $RDF->c($id);
        return $sx;
    }
}