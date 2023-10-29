<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RDFvalidate extends Model
{
    var $DBGroup          = 'default';
    var $table            = 'rdfpdfs';
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

    function index($id = 0)
        {
            $id = 199113;
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $RDF->le($id);
            pre($dt);
        }

    function show($id)
    {
        $sx = '';
        $sx .= '<div class="btn btn-outline-primary mt-2" style="width: 100%;">';
        $sx .= '<table width="100%">';
        $sx .= '<tr><td>';
        $sx .= 'NLP';
        $sx .= '</td><td>';
        $sx .= $this->version;
        $sx .= '</td><td>';
        $sx .= '</table>';
        $sx .= '</div>';
        return $sx;
    }

    function validadePROCEEDING()
        {

        }

}
