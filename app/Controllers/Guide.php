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
    public function index($act = '', $subact = '', $id = '', $id2 = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Revistas';
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

        $q = get("q") . get("qs");
        if (strlen($q) > 0) {
            $act = 'search';
        }

        $act = trim($act);

        switch ($act) {

            default:
                $id = 75;
                $data['logo'] = view('Logos/logo_benancib');
                $sx .= view('Brapci/Pages/search');

                $Events = new \App\Models\Functions\Event();
                $sx .= $Events->index('cards');
                //$sx .= view('Brapci/Welcome', $data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
