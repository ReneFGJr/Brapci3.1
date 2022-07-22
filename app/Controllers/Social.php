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
define("COLLECTION",'');

class Social extends BaseController
{
    function ajax($act='')
        {
            $Socials = new \App\Models\Socials();
            $sx = $Socials->ajax($act);
            return $sx;
        }
    public function index($act='',$subact='',$id='')
    {
        $act .= get("cmd");
        $data['page_title'] = 'Brapci - Login IDP';
        $sx = view('Brapci/Headers/header',$data);
        $sx .= view('Brapci/Headers/navbar',$data);        
        $Socials = new \App\Models\Socials();
        switch ($act)
            {
                case 'logout':
                    $Socials->logout();
                    return redirect('MainPages::index');
                break;

                default:
                    $sa = h($act.'.'.$subact);
                    $sa .= $Socials->index($act,$subact,$id);
                    break;
            }
       
        $sx .= bs(bsc($sa,12));
        $sx .= view('Brapci/Headers/footer',$data);

        return $sx;
    }
}
