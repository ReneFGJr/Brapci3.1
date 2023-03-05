<?php

namespace App\Controllers;

use App\Controllers\BaseController;

///* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');
define("COLLECTION", '/social');

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
        $data['GOOGLEID'] = 'UA-12713129-1';
        $data['bg'] = 'bg-primary';
        $data['bg_color'] = '#0000ff';

        $sx = view('Brapci/Headers/header',$data);
        $sx .= view('Brapci/Headers/navbar',$data);
        $Socials = new \App\Models\Socials();
        switch ($act)
            {
                case 'oauth2':
                    $Oauth2 = new \App\Models\Oauth2\Index();
                    $Oauth2->oauth2_feedback();
                    break;
                    //return redirect('MainPages::index');
                case 'logout':
                    $Socials->logout();
                    return redirect('MainPages::index');
                break;

                default:
                    $sa = $Socials->index($act,$subact,$id);
                    break;
            }

        $sx .= bs(bsc($sa,12));
        $sx .= view('Brapci/Headers/footer',$data);

        return $sx;
    }
}
