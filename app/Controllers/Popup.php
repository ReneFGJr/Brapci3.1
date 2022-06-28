<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr','sessions','cookie']);
$session = \Config\Services::session();

define("URL",getenv("app.baseURL"));
define("PATH",getenv("app.baseURL").'/');
define("MODULE",'');
define("PREFIX",'');

class Popup extends BaseController
{
    public function index($act='')
    {
        $data['page_title'] = 'Brapci - POPUP - '.ucfirst($act);
        $data['bg'] = 'bg-pq';
        $sx = '';
        $sx .= view('Brapci/Headers/header',$data);

        switch($act)
            {
                case 'pq_bolsa_edit':
                    $Bolsas = new \App\Models\PQ\Bolsas();
                    $id = get('id');
                    $sx .= $Bolsas->edit($id);
                    break;
            }
        return $sx;
    }
}
