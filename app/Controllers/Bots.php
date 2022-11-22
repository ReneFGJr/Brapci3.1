<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/bots');
define("PREFIX", '');
define("LIBRARY", '1000');

class Bots extends BaseController
{
    public function index($act = '')
    {
        $sx = '';
        if ($act == 'patent') { $act = 'patente'; }

        switch ($act) {

            case 'patente':
                $Patente = new \App\Models\Patent\Index;
                $Patente->cron();
                break;
            case 'pdf':
                $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                $sx .= $DownloadPDF->harvesting();
            default:
                $menu = array();
                $menu[PATH . COLLECTION . '/pdf'] = lang('bots.harvesting_pdf');
                echo menu($menu);
                break;
        }
        $sx .= '</pre>';
        return $sx;
    }
}