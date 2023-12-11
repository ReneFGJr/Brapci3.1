<?php
/*
@category API
@package Brapci Labels
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example https://brapci.inf.br/api/labels/test
@abstract API para uso do Lattes
*/
namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class LabelPrint extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'find_labels_model';
    protected $primaryKey       = 'id_lb';
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

    function index($d1,$d2,$d3)
        {
            $dt = [];
            $da = [];
            for ($r=0;$r < 200;$r++)
                {
                    array_push($da,['ln1'=>'205.10','ln2'=>'A123A']);
                }
            $dt['labels'] = $da;

            switch($d2)
                {
                    case 'test':
                        $mpdf = new \Mpdf\Mpdf();
                        $html = view('BrapciBooks/Labels/pimax_01.html',$dt);
                        $mpdf->WriteHTML($html);
                        header("Content-type:application/pdf");
                        $mpdf->Output('labels.pdf','I'); // opens in browser
                        //$mpdf->Output('arjun.pdf','D'); // it downloads the file into the user system, with give name
                        //return view('welcome_message');
                        exit;
                        break;
                }

        }
}
