<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFfile extends Model
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

    function fileType($file)
    {
        $ext = '.xxx';
        $type = mime_content_type($file);
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
                $ext = '.pdf';
                break;
        }
        return $ext;
    }

    function savePDF($ID,$prop,$file)
        {
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFliteral = new \App\Models\RDF2\RDFliteral();
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $RDFproperty = new \App\Models\RDF2\RDFproperty();
            $propID = $RDFproperty->getProperty($prop);
            $dir = $this->directory($ID);
            $ext = $this->fileType($file);
            $filename = 'work_' . strzero($ID, 10) . '.pdf';
            $dest = $dir . $filename;

            $dt = [];
            $dt['Name'] = $filename;
            $dt['Lang'] = 'nn';
            $dt['Class'] = 'FileStorage';
            $idc = $RDFconcept->createConcept($dt);

            if ($idc <= 0)
                {
                    $RSP = [];
                    $RSP['status'] = '500';
                    $RSP['message'] = 'Erro ao salvar dados RDF: ' . $e->getMessage();
                    return $RSP;
                }

            /***************************** Data */
            copy($file, $dest);
            unlink($file);

            /************************** Incula Imagem com Conceito */
            $RDFdata->register($ID, 'hasFileStorage', $idc, 0);

            /***************************************** ContentType */
            $dt = [];
            $dt['Name'] = 'PDF';
            $dt['Lang'] = 'nn';
            $dt['Class'] = 'ContentType';
            $idt = $RDFconcept->createConcept($dt);
            $RDFdata->register($idc, 'hasContentType', $idt, 0);

            /***************************************** Literal Directory */
            $name = $dir;
            $prop = 'hasFileDirectory';
            $lang = 'nn';
            $RDFconcept->registerLiteral($idc, $name, $lang, $prop);


            /***************************************** Literal hasFileName */
            $name = $dest;
            $prop = 'hasFileName';
            $lang = 'nn';
            $RDFconcept->registerLiteral($idc, $name, $lang, $prop);

            $RSP = [];
            $RSP['prop'] = $prop;
            $RSP['propID'] = $propID;
            $RSP['ID'] = $ID;
            $RSP['file'] = $file;
            $RSP['dest'] = $dest;
            $RSP['status'] = '200';
            return $RSP;
        }


    function upload($d1 = '', $d2 = '')
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
                if (isset($_FILES['file'])) {
                    $RSP['PID'] = $BooksSubmit->registerPDF();
                    $RSP['status'] = '200';
                } else {
                    $RSP['status'] = '400';
                    $RSP['message'] = 'Arquivo não enviado';
                }
                echo json_encode($RSP);
                exit;
                break;
            case 'cover':
                $idc = $this->saveImage($ID);
                //$RDFdata->register($ID,'hasCover',$idc,0);
                $status = 'SAVED ' . $ID . '-' . $idc;
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
                $dd['erro'] = 'Tipo ' . $d1 . ' não existe';
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


    function directory($id, $pre = '_repository/')
    {
        $id = strzero($id, 8);
        $file = $pre . substr($id, 0, 2) . '/' . substr($id, 2, 2) . '/' . substr($id, 4, 2) . '/' . substr($id, 6, 2) . '/';
        dircheck($file);
        return $file;
    }

}