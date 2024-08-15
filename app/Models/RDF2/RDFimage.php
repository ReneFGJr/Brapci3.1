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

    function upload($d1='', $d2='')
    {
        $RSP = [];
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $idc = 0;
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }

        $ID = $d2;
        $status = 'NONE';
        switch ($d1) {
            case 'pdfBOOK':
                $BooksSubmit = new \App\Models\Books\BooksSubmit();
                $RSP = [];
                $RSP['PID'] = $BooksSubmit->registerPDF();
                $RSP['status'] = '200';
                echo json_encode($RSP);
                break;
            case 'cover':
                $idc = $this->saveImage($ID);
                //$RDFdata->register($ID,'hasCover',$idc,0);
                $status = 'SAVED '.$ID.'-'.$idc;
                break;
            case 'image':
                $RSP = $this->saveImage($ID);
                $RSP['id'] = $ID;
                $RSP['files'] = $_FILES;
                echo json_encode($RSP);
                exit;
                break;
            case 'pdf':
                $idc = $this->savePDF($ID);
                $RDFdata->register($ID, 'hasFileStorage', $idc, 0);
                //$status = 'SAVED ' . $ID . '-' . $idc;
                $dd = [];
                $dd['status'] = '200';
                $dd['ID'] = $idc;
                echo json_encode($dd);
                break;
            default:
                $dd = [];
                $dd['erro'] = 'Tipo '.$d1.' não existe';
                echo json_encode($dd);
                exit;

        }
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
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        /********************************************** */

        $da = $RDF->le($ID);
        $ccClass = $da['concept']['c_class'];
        $ttt = 'Indefinido';
        $da['ID'] = $ID;

        $RSP = [];
        $RSP['status'] = '502';
        $RSP['message'] = 'Não finalizado';
        $RSP['files'] = $_FILES;
        $RSP['type'] = $ccClass;

        if (isset($_FILES['file']))
            {
            $fileName = $_FILES['file']['name'];
            $tmp = $_FILES['file']['tmp_name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];
            $ext = '.qqq';
            $dire = '_repository';

            switch ($ccClass) {
                case 'Book':
                    $dire = $this->directory($ID, '_repository/book/');
                    $ttt = 'book';
                    $ext = '.pdf';
                    $RSP['directory'] = $dire;
                    break;
                case 'pdfx':
                    $dire = $this->directory($ID);
                    $ttt = 'article';
                    $ext = '.xxx';
                default:
                    echo json_encode($dd = [$ccClass, 'type']);
                    exit;
                    $dd['Erro'] = $ccClass . ' não foi mapeada';
                    echo json_encode($dd);
                    exit;
                    break;
            }

            $dest = $dire . $ttt . $ext;
            $RSP['destinity'] = $dest;
            move_uploaded_file($tmp, $dest);

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

            $dd = [];
            $dd['status'] = '200';
            $dd['tmp'] = $tmp;
            $dd['dest'] = $dest;
            $dd['idc'] = $idc;
            $dd['ID'] = $ID;
            } else {
                $dd['status'] = '400';
                $dd['message'] = 'File not put';
                $dd['files'] = $_FILES;
            }

        echo json_encode($dd);
        exit;

        return $idc;
    }

    function saveImage($ID)
    {
        $RSP = [];
        if (!isset($_FILES['file']['name']))
            {
                $RSP['status'] = '500';
                $RSP['message'] = 'Erro ao carregar o arquivo.';
                return $RSP;
            }
        $fileName = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];

        $name = md5($ID);

        $dire = $this->directory($ID);
        $RSP['directory'] = $dire;
        $RSP['property'] = get("property");

        $ext = '.xxx';

        switch ($type) {
            case 'image/jpeg':
                $ext = '.jpg';
                break;
            case 'image/png':
                $ext = '.png';
                break;
            case 'image/gif':
                $ext = '.gif';
                break;
            default:
                $dd = [];
                $RSP['type'] = $type;
                $RSP['status'] = '400';
                $RSP['message'] = 'Format Invalid ('.$type.')';
                echo json_encode($RSP);
                exit;
        }
        $dest = $dire . 'image' . $ext;
        move_uploaded_file($tmp, $dest);

        /********************************************** */
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        /* Create concept */
        $dt = [];
        $dt['Name'] = $dest;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'Image';

        $IDC = $RDFconcept->createConcept($dt);
        $RSP['IDC'] = $IDC;

        /************************** Incula Imagem com Conceito */
        $RDFdata->register($ID, get("property"), $IDC, 0);

        /***************************************** ContentType */
        $dt = [];
        $dt['Name'] = $type;
        $dt['Lang'] = 'nn';
        $dt['Class'] = 'ContentType';
        $idt = $RDFconcept->createConcept($dt);
        $RDFdata->register($IDC, 'hasContentType', $idt, 0);

        /***************************************** Literal Directory */
        $name = $dire;
        $prop = 'hasFileDirectory';
        $lang = 'nn';
        $RDFconcept->registerLiteral($IDC, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $fileName;
        $prop = 'hasFileName';
        $lang = 'nn';
        $RDFconcept->registerLiteral($IDC, $name, $lang, $prop);

        /***************************************** Literal hasFileName */
        $name = $size;
        $prop = 'hasFileSize';
        $lang = 'nn';
        $RDFconcept->registerLiteral($IDC, $name, $lang, $prop);

        return $RSP;
    }
    function directory($id,$pre= '_repository/')
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
