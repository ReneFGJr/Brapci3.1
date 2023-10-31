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
            /* NAO USADO PARA AS APIS */
            if ($d1 != 'import')
                {
                    header("Content-Type: application/json");
                }

            $RSP = [];
            switch($d1)
                {   case 'import':
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
            $data = [];
            $data['concept'] = $RDFconcept->le($id);
            return $data;
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
}
