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

class Main extends BaseController
{
    public function index()
    {
        $data['page_title'] = 'Brapci';
        $sx = '';
        $sx .= view('Brapci/Headers/header',$data);
        $sx .= view('Brapci/Headers/navbar',$data);
        $sx .= view('Brapci/Svg/logo_brapci',$data);
        $sx .= view('Brapci/Headers/footer',$data);
        return $sx;
    }
}
