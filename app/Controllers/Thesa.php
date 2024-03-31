<?php

namespace App\Controllers;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

use App\Controllers\BaseController;

class Thesa extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $Term = new \App\Models\Thesa\Term();
        $Concept = new \App\Models\Thesa\Concept();

        $RSP = [];
        $RSP = $this->thesa_header($RSP);
        //
        switch ($d1) {
            case 'conecpt':
                $RSP2 = $Concept->getID($d2);
                $RSP = array_merge($RSP, $RSP2);
                $this->show_json($RSP);
                break;
            case 'term':
                switch ($d2) {
                    case 'get':
                        $RSP2 = $Term->getID($d3);
                        $RSP = array_merge($RSP, $RSP2);
                        $this->show_json($RSP);
                        break;

                    case 'add':
                        $th = 5;
                        $term = get("term");
                        $lang = get("lang");
                        $RSP2 = $Term->add($term, $lang, $th);
                        $RSP = array_merge($RSP, $RSP2);
                        $this->show_json($RSP);
                        break;
                }
            case 'th':
                $TH = new \App\Models\Thesa\Thesaurus();
                $auth = $this->authenticator();

                if ($d2 == '') {
                    $dt = $TH->list($auth);
                    $RSP['thesaurus'] = $dt;
                } else {
                    $RSP = $TH->get($d2);
                }
                $this->show_json($RSP);
                break;
            default:
                $this->header_json();
                $RSP = [];
                $RSP = $this->thesa_header($RSP);
                $RSP['status'] = '200';
                $RSP['message'] = 'Empty';
                $this->show_json($RSP);
                break;
        }
    }

    private function authenticator()
    {
        $api = get("apikey");
        return 1;
    }

    private function thesa_header($RSP)
    {
        $RSP['app'] = 'Thesa';
        $RSP['app_version'] = '2.1';
        return $RSP;
    }

    private function show_json($RSP)
    {
        echo json_encode($RSP);
        exit;
    }

    private function header_json()
    {
        /* NAO USADO PARA AS APIS */
        header('Access-Control-Allow-Origin: *');


        if (get("test") == '') {
            header("Access-Control-Allow-Headers: Content-Type");
            header("Content-Type: application/json");
        }
    }
}
