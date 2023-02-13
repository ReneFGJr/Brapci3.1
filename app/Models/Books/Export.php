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
                    $idc = $RDF->conecpt($name,$lang);
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
