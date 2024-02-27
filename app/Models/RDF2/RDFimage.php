<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFimage extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfimages';
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

    function upload($d1, $d2)
    {
        $RDFdata = new \App\Models\RDF2\RDFdata();

        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }

        $ID = $d2;
        $status = 'NONE';
        switch ($d1) {
            case 'cover':
                $idc = $this->saveImage($ID);
                $RDFdata->register($ID,'hasCover',$idc,0);
                $status = 'SAVED '.$ID.'-'.$idc;
                break;
            case 'pdf':
                $idc = $this->savePDF($ID);
                echo $idc;
                exit;
                $RDFdata->register($ID, 'hasFileStorage', $idc, 0);
                $status = 'SAVED ' . $ID . '-' . $idc;
                break;
            default:
                $dd = [];
                $dd['erro'] = 'Tipo '.$d1.' não existe';
                echo json_encode($dd);
                exit;

        }
        $RSP = [];
        $RSP['id'] = $idc;
        $RSP['d1'] = $d1;
        $RSP['d2'] = $d2;
        $RSP['status'] = $status;
        $RSP['files'] = $_FILES;
        echo json_encode($RSP);
        exit;
    }

    function savePDF($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $da = $RDF->le($ID);
        $ccClass = $da['concept']['c_class'];
        $ttt = 'Indefinido';
        $da['ID'] = $ID;

        $fileName = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];

        echo json_encode($dd = [$ccClass,'type']);
        exit;

        switch ($ccClass)
            {
                case 'Book':
                    $dire = $this->directory($ID, '_repository/book/');
                    $ttt = 'book';
                    break;
                case 'pdf':
                    $x = '1';
                default:
                    $dd['Erro'] = $ccClass .' não foi mapeada';
                    echo json_encode($dd);
                    exit;
                    break;
            }

        $ext = '.xxx';

        switch ($type) {
            case 'application/pdf':
                $ext = '.pdf';
                break;
            default:
                $dd = [];
                $dd['type'] = $type;
                echo json_encode($dd);
                exit;
        }
        $dest = $dire . $ttt . $ext;
        move_uploaded_file($tmp, $dest);

        /********************************************** */
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        /* Create concept */
        $dt = [];
        $name = $dest;
        $dt['Name'] = $name;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'FileStorage';
        $idc = $RDFconcept->createConcept($dt);

        /************************** Incula Imagem com Conceito */
        $RDFdata->register($ID, 'hasFileStorage', $idc, 0);

        /***************************************** ContentType */
        $dt = [];
        $dt['Name'] = $type;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'ContentType';
        $idt = $RDFconcept->createConcept($dt);
        $RDFdata->register($idc, 'hasContentType', $idt, 0);

        /***************************************** Literal Directory */
        $name = $dire;
        $prop = 'hasFileDirectory';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $fileName;
        $prop = 'hasFileName';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $fileName;
        $prop = 'hasFileName';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        return $idc;
    }

    function saveImage($ID)
    {
        $fileName = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];

        $name = md5($ID);

        $dire = $this->directory($ID);
        $ext = '.xxx';

        switch ($type) {
            case 'image/jpeg':
                $ext = '.jpg';
                break;
            case 'image/png':
                $ext = '.png';
                break;
            default:
                $dd = [];
                $dd['type'] = $type;
                echo json_encode($dd);
                exit;
        }
        $dest = $dire . 'image' . $ext;
        move_uploaded_file($tmp, $dest);

        /********************************************** */
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        /* Create concept */
        $dt = [];
        $dt['Name'] = $name;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'Image';
        $idc = $RDFconcept->createConcept($dt);

        /************************** Incula Imagem com Conceito */
        $RDFdata->register($ID, 'hasCover', $idc, 0);

        /***************************************** ContentType */
        $dt = [];
        $dt['Name'] = $type;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'ContentType';
        $idt = $RDFconcept->createConcept($dt);
        $RDFdata->register($idc, 'hasContentType', $idt, 0);

        /***************************************** Literal Directory */
        $name = $dire;
        $prop = 'hasFileDirectory';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $fileName;
        $prop = 'hasFileName';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $fileName;
        $prop = 'hasFileName';
        $lang = 'nn';
        $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

        return $idc;
    }
    function directory($id,$pre='img/c')
    {
        $id = strzero($id, 8);
        $file = $pre . substr($id, 0, 2) . '/' . substr($id, 2, 2) . '/' . substr($id, 4, 2) . '/' . substr($id, 6, 2) . '/';
        dircheck($file);
        return $file;
    }


    function cover($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $dti = $RDF->le($ID);
        $data = $dti['data'];

        $dir = '';
        $file = '';
        $tumb = '';
        $type = '';

        foreach ($data as $id => $line) {
            $prop = $line['Property'];
            if ($prop == 'hasFileDirectory') {
                $dir = $line['Caption'];
            }
            if ($prop == 'hasContentType') {
                $type = $line['Caption'];
            }

            if ($prop == 'hasTumbNail') {
                $tumb = $line['Caption'];
            }
        }

        if ($dir != '') {
            switch ($type) {
                case 'image/jpeg':
                    $nfile = $dir . 'image.jpg';
                    if (file_exists($nfile)) {
                        $url = PATH . '/' . $nfile;
                        return $url;
                    } else {
                        return PATH . '/img/cover/no_cover.png';
                    }

                    if (file_exists($tumb)) {
                        return PATH . $tumb;
                    }

                    break;
                case 'image/png':
                    $nfile = $dir . 'image.png';
                    if (file_exists($nfile)) {
                        return PATH . '/' . $nfile;
                    }

                    if (file_exists($tumb)) {
                        return PATH . '/' . $tumb;
                    }

                    break;
            }
        }
        return PATH . '/img/cover/no_cover.png';
    }
}
