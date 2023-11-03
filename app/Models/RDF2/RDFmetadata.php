<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFmetadata extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfmetadatas';
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

    function simpleMetadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDF->le($ID);
        $dd = [];

        $sm = [
            'hasTitle' => [],
            'hasCover' => []
        ];

        $da = $dt['data'];
        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];

            if (isset($sm[$prop])) {
                if (!isset($dd[$prop][$lang])) {
                    $dd[$prop][$lang] = [];
                }
                $dc = [];
                $dc[$line['Caption']] = $line['ID'];
                array_push($dd[$prop][$lang], $dc);
            }
        }

        /*************** IDIOMA Preferencial */
        $lg = $this->langPref();
        $de = [];
        foreach ($sm as $prop => $line) {
            foreach ($lg as $id => $lang) {
                if (isset($dd[$prop][$lang])) {
                    if (!isset($de[$prop])) {
                        if (isset($dd[$prop][$lang][0])) {
                            $vlr = $dd[$prop][$lang][0];
                            $de[$prop] = trim(key($vlr));
                        }
                    }
                }
            }
        }

        $dr['ID'] = $ID;
        $dr['data'] = $de;
        return $dr;
    }

    function langPref()
        {
            $dt = ['pt', 'es', 'en', 'nn'];
            return $dt;
        }

    function metadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDF->le($ID);
        $dd = [];
        $dd['section'] = 'none';
        $dd['cover'] = 'none';
        $dd['title'] = 'none';
        $dd['section'] = 'none';
        $dd['section'] = 'none';

        $da = $dt['data'];
        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];
            if (!isset($dd[$prop][$lang])) {
                $dd[$prop][$lang] = [];
            }
            $dc = [];
            $dc[$line['Caption']] = $line['ID'];
            array_push($dd[$prop][$lang], $dc);
        }
        $dr['ID'] = $ID;
        $dt['title'] = $this->simpleExtract($dd,'hasTitle');
        $dr['data'] = $dd;
        return $dr;
    }

    function simpleExtract($dt,$class)
        {
            $lang = $this->langPref();
            echo h($class,2);
            if (isset($dt[$class]))
                {
                    foreach($dt as $nn=>$line)
                        {
                            if ($nn == $class)
                                {
                                    foreach()
                                }
                        }
                }
            exit;
        }
}
