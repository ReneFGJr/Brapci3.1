<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');
define("COLLECTION", '');

class Guide extends BaseController
{
    public function index($act = '', $subact = '', $id = '', $id2 = '',$d3='',$d4='')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Guide';
        $data['GOOGLEID'] = 'UA-12713129-1';
        $data['bg'] = 'bg-primary';
        $data['bg_color'] = '#0000ff';
        $menu = array();
        $menu[PATH . '/guide'] = lang('brapci.guide');

        $data['menu'] = $menu;
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        $m = [];
        $m['Brapci'] = PATH;
        $m[lang('brapci.guide')] = PATH . '/guide';
        $sx .= breadcrumbs($m);

        $act = trim($act);

        switch ($act) {
            case 'software':
                $data['page_title'] = lang('brapci.software');
                $data['bg'] = 'bg-success';
                $data['bg_color'] = '#00ff00';
                $data['menu'][PATH . '/guide/software'] = lang('brapci.software');
                $Guide = new \App\Models\Guide\Software\Index();
                $sx .= $Guide->index($subact, $id);
                break;
            case 'popup':
                $sx = view('Brapci/Headers/header', $data);
                $Guide = new \App\Models\Guide\Index();
                $sx .= $Guide->index($act, $subact, $id,$id2, $d3, $d4);
                return $sx;
                break;

            default:
                $Guide = new \App\Models\Guide\Index();
                $sx .= $Guide->index($act,$subact, $id, $id2,$d3,$d4);
                //$sx .= view('Brapci/Welcome', $data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
