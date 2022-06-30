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
define("COLLECTION",'benancib');

class Benancib extends BaseController
{
    public function index($act='')
    {
        $Issues = new \App\Models\Base\Issues();
        $id = 75;
        $data['page_title'] = 'Brapci-Benancib';
        $data['bg'] = 'bg-benancib';
        $sx = '';
        $sx .= view('Brapci/Headers/header',$data);
        $sx .= view('Benancib/Headers/navbar',$data);

        switch($act)
            {
                case 'issue':
                $sx .= 'ISSUE';
                break;
                default:
                $data['logo'] = view('Benancib/Svg/logo_benancib');
                $data['issues'] = $Issues->show_list_cards($id);
                $sx .= view('Benancib/Welcome',$data);
                break;
            }

        $sx .= view('Brapci/Headers/footer',$data);
        return $sx;
    }


}
