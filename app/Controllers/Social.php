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

class Social extends BaseController
{
    function ajax($act='')
        {
            $Socials = new \App\Models\Socials();
            $sx = $Socials->ajax($act);
            return $sx;
        }
    public function index()
    {
        $data['page_title'] = 'Brapci - Login IDP';
        $sx = view('Brapci/Headers/header',$data);
        $sx .= view('Brapci/Headers/navbar',$data);        
        $Socials = new \App\Models\Socials();
        $sa = $Socials->login();
        $sx .= bs(bsc($sa,12));
        $sx .= view('Brapci/Headers/footer',$data);

        return $sx;
    }
}
