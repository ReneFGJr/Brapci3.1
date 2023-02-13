<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class Export extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'exports';
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

    function index_authors()
        {
            $RDF = new \App\Models\Rdf\RDF();
            $class = "Book";
            $idc = $RDF->getClass($class);

            $RDFConcept = new \App\Models\RDF\RDFConcept();
            $dt = $RDFConcept->select('id_cc')->where('cc_class',$idc)->findAll();
            $index = array();
            foreach($dt as $id=>$line)
                {
                    $idb = $line['id_cc'];
                    $dtb = $RDF->le($idb);

                    $orgs = $RDF->extract($dtb, 'hasOrganizator');
                    $auth = $RDF->extract($dtb, 'hasAuthor');
                    $authors = array_merge($orgs,$auth);
                    foreach($authors as $idx=>$ida)
                        {
                            if (!isset($index[$ida])) { $index[$ida] = 1; }
                        }
                }

                $sx = '';
                $row = [];
                foreach($index as $ida=>$id)
                    {
                        $name = $RDF->c($ida);
                        $name_strip = strip_tags($name);
                        $row[$name_strip] = $name;
                    }
                ksort($row);
                $xlt = '';
                foreach($row as $name=>$link)
                    {
                        $lt = substr(UpperCaseSQL($name),0,1);
                        if ($lt != $xlt)
                            {
                                $sx .= h($lt);
                                $xlt = $lt;
                            }
                        $sx .= '<li>'.$link.'</li>';
                    }
                return $sx;
            }

    function index_classes()
        {
            $sx = '';
            $RDF = new \App\Models\Rdf\RDF();
            $Class = 'ClassificationAncib';
            $idc = $RDF->getClass($Class);

            $RDFData = new \App\Models\Rdf\RDFData();
            $sql = "select * from rdf_data where d_p = ".$idc;
            $dt = $RDFData->query($sql)->getResult();

            $Classes = new \App\Models\Books\ClassificationAncib();
            $dl = $Classes->where('bs_rdf',0)->findAll();

            foreach($dl as $id=>$line)
                {
                    $lang = 'pt-BR';
                    $name = $line['bs_name'];
                    $idc = $RDF->conecpt($name,$Class);
                    $line['bs_rdf'] = $idc;
                    $line['update_at'] = date("Y-m-d H:i:s");
                    $Classes->set($line)->where('id_bs',$line['id_bs'])->update();
                    $sx .= '<br>Update '.$name;
                }

            if (count($dl) == 0)
                {
                    $sx .= bsmessage('Nothing to export',1);
                }
            return $sx;
        }
}
