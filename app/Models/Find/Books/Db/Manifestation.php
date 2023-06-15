<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Manifestation extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'manifestations';
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

    function register($ide,$dt)
        {
            foreach($dt as $prop=>$vlr)
                {
                    echo $prop;
                    echo '<hr>';
                    switch($prop)
                        {
                            case 'editora':
                                $this->editoras($ide,$vlr);
                                break;
                        }
                }
        }

        function editoras($ide,$vlr)
            {
                $class = "brapci:Publisher";
                $RDF = new \App\Models\Find\Rdf\RDF();

                $RDFConcept = new \App\Models\Rdf\RDFConcept();
                $RDFConcept->table = 'find.' . $RDFConcept->table;

                $RDFLiteral = new \App\Models\Rdf\RDFLiteral();
                $RDFLiteral->table = 'find.'. $RDFLiteral->table;

                $id_class = $RDF->class($class);
                foreach($vlr as $id=>$name)
                    {
                        $id_literal = $RDF->literal($name,'NnN',True);
                        $idc = $RDF->concept($id_class,$id_literal);
                    }

                echo h($id_class);
                echo h($id_literal);
                pre($vlr);
            }
}
