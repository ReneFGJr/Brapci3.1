<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'dci');

class Dci extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 ='', $d4 ='', $d5 = '')
    {
        $sx = '';

        $data['page_title'] = 'DCI - UFRGS';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('DCI/Headers/header', $data);
        $sx .= view('DCI/Headers/navbar', $data);

        $DCI = new \App\Models\Dci\Index();
        $sx .= $DCI->index($d1,$d2,$d3,$d4,$d5);
        $sx .= view('DCI/Headers/footer', $data);
        return $sx;
    }

    public function capes($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $sx = '';
        $data['page_title'] = 'CAPES';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('DCI/Headers/header', $data);
        $sx .= view('DCI/Headers/navbar', $data);
        $Capes = new \App\Models\Capes\Capes();

        if (get("ppg") != '') {
            $Capes->setPpg(get("ppg"));
            $PPG = get("ppg");
            $d2 = '';
        } else {
            $PPG = $Capes->getPpg();
        }

        switch ($d2) {
            case 'view':
                $dd = [];
                $dd['ppg'] = $Capes->getPpg();
                $dd['sf'] = $Capes->view($d3);
                $dd['form'] = view('Capes/form_item', $Capes->data);
                $sx .= view('Capes/form_ppg', $dd);
                break;
            default:
                $dd = [];
                $dd['ppg'] = $Capes->getPpg();
                $dd['sf'] = $Capes->view();
                $dd['form'] = view('Capes/form_item', $Capes->data);
                $sx .= view('Capes/form_ppg', $dd);
                break;
        }
        return $sx;
    }
}