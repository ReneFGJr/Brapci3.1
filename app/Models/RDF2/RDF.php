<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfs';
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

    function index($d1,$d2,$d3,$d4)
        {
            $RSP = [];
            switch($d1)
                {
                    case 'import':
                        $RSP = $this->import();
                        break;
                    case 'source':
                        break;
                    default:
                        $RSP = $this->default();
                        break;
                }
            $RSP['time'] = date("Y-m-dTH:i:s");
            echo json_encode($RSP);
            exit;
        }

    /************* Default */
    function default()
        {
            $dt = [];
            $dt['status'] = '404';
            $dt['message'] = 'Action not informed';
            return $dt;
        }

    /************* V */
    function v($id)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFdata = new \App\Models\RDF2\RDFdata();

            $data = [];
            $data['concept'] = $RDFconcept->le($id);

            $data['data'] = $RDFdata->le($id);

            return $data;
        }

    /************* V */
    function resume()
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->select('count(*) as total, c_class')
            ->join('rdf_class','id_c = cc_class')
            ->groupBy('c_class')
            ->orderBy('c_class')
            ->findAll();
        $d = [];
        foreach($dt as $id=>$line)
            {
                $class = $line['c_class'];
                $d[$class] = $line['total'];
            }
        pre($d);

    }

    function le($id)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $d = [];
            $d['concept'] = $RDFconcept->le($id);
            $d['data'] = $RDFdata->le($id);

            /************************* Remover */
            if ($d['data'] == [])
                {
                    $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
                    $RDFtoolsImport->importRDF($id);
                    $d['data'] = $RDFdata->le($id);
                }
            return $d;
        }

    function valid($id)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $dt = $RDFconcept->find($id);
            if ($dt == null)
                {
                    return false;
                } else {
                    return true;
                }
        }

    /************* Import */
    function import()
        {
            $sx = '';
            $sx .= form_open_multipart();
            $sx .= form_upload('OWL');
            $sx .= form_submit('action','Send file');
            $sx .= form_close();

            if (isset($_FILES['OWL']))
                {
                    $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
                    $file = $_FILES['OWL']['tmp_name'];

                    $sx .= $RDFtoolsImport->import($file);
                }
            echo $sx;
            exit;
        }

    function recoverClass($class,$limit=20,$offset=0,$ord='N')
        {
            $ord = substr($ord,0,1);
            $RDFclass = new \App\Models\RDF2\RDFclass();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            if ((sonumero($class)) != $class)
                {
                    $class = $RDFclass->getClass($class);
                }
            if ($limit == '') { $limit = 20; }
            $dt = $RDFconcept
                ->select('id_cc')
                ->where('cc_class',$class);
            if ($ord = 'd')
                {
                    $RDFconcept->orderBy('id_cc desc');
                }
            $dt = $RDFconcept->findAll($limit,$offset);
            return $dt;
        }
    function extract($dt,$prop,$type='F')
        {
            /*
            F->first
            A->Array
            S->string (todos)
            */
            $dt = $dt['data'];
            $dr = [];
            $st = '';

            foreach($dt as $id=>$line)
                {
                    if ($line['Property'] == $prop)
                        {
                            /******************************** FIRST */
                            if ($type == 'F') { return($line['Caption']); }
                        }
                }
            if ($type == 'A') { return $dr; }
            if ($type == 'S') { return $st; }
        }
}
